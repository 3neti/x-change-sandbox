<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\QueueOperationsFailureCanaryJob;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Contracts\Redis\Factory as RedisFactory;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Horizon\Contracts\JobRepository;
use Throwable;

#[Signature('settlement-os:queues:cleanup-failure {jobId : Failed job UUID} {canaryId : Synthetic failure canary ULID} {--json : Emit JSON}')]
#[Description('Remove one exact synthetic failure canary from failed-job, Horizon, and marker storage')]
final class SettlementQueuesCleanupFailureCommand extends Command
{
    public function handle(JobRepository $jobs, RedisFactory $redis): int
    {
        $jobId = trim((string) $this->argument('jobId'));
        $canaryId = trim((string) $this->argument('canaryId'));
        $database = (string) config('queue.failed.database');
        $table = (string) config('queue.failed.table');

        $errors = array_values(array_filter([
            app()->environment(['local', 'testing']) ? null : 'Cleanup is restricted to local and testing environments.',
            config('queue.failed.driver') === 'database-uuids' ? null : 'The failed-job provider must use database UUIDs.',
            Str::isUuid($jobId) ? null : 'The job ID must be a UUID.',
            Str::isUlid($canaryId) ? null : 'The canary ID must be a ULID.',
            $database !== '' ? null : 'The failed-job database is not configured.',
            $table !== '' ? null : 'The failed-job table is not configured.',
        ]));

        if ($errors !== []) {
            return $this->respond('blocked', $jobId, $canaryId, $errors);
        }

        if (! Schema::connection($database)->hasColumn($table, 'uuid')) {
            return $this->respond('blocked', $jobId, $canaryId, [
                "The failed-job table [{$table}] does not expose a UUID column.",
            ]);
        }

        $connection = DB::connection($database);
        $failedJobs = $connection->table($table)->where('uuid', $jobId)->limit(2)->get();
        $horizonJob = $jobs->findFailed($jobId);

        if ($failedJobs->count() > 1) {
            return $this->respond('blocked', $jobId, $canaryId, [
                'Multiple failed-job records matched the requested UUID.',
            ]);
        }

        $databaseJob = $failedJobs->first();
        $validationErrors = array_merge(
            $this->validateDatabaseJob($databaseJob, $canaryId),
            $this->validateHorizonJob($horizonJob, $canaryId),
        );

        if ($validationErrors !== []) {
            return $this->respond('blocked', $jobId, $canaryId, $validationErrors);
        }

        try {
            $databaseDeleted = $this->deleteDatabaseJob($connection, $table, $databaseJob, $jobId);
            $horizonDeleted = $horizonJob === null ? 0 : $jobs->deleteFailed($jobId);
            $markerKeysDeleted = (int) $redis
                ->connection(config('queue.connections.redis.connection'))
                ->command('del', [
                    QueueOperationsFailureCanaryJob::markerKey($canaryId),
                    QueueOperationsFailureCanaryJob::attemptsKey($canaryId),
                ]);
        } catch (Throwable) {
            return $this->respond('cleanup_failed', $jobId, $canaryId, [
                'Cleanup stopped after a storage operation failed. Rerun the same command to recover the remaining exact state.',
            ]);
        }

        if ($connection->table($table)->where('uuid', $jobId)->exists() || $jobs->findFailed($jobId) !== null) {
            return $this->respond('cleanup_failed', $jobId, $canaryId, [
                'The exact synthetic failure record is still present after cleanup.',
            ]);
        }

        return $this->respond(
            $databaseJob === null && $horizonJob === null ? 'already_clean' : 'cleaned',
            $jobId,
            $canaryId,
            [],
            [
                'database_records_deleted' => $databaseDeleted,
                'horizon_records_deleted' => $horizonDeleted,
                'marker_keys_deleted' => $markerKeysDeleted,
            ],
            self::SUCCESS,
        );
    }

    /** @return list<string> */
    private function validateDatabaseJob(?object $job, string $canaryId): array
    {
        if ($job === null) {
            return [];
        }

        return array_values(array_filter([
            $job->connection === QueueOperationsFailureCanaryJob::Connection ? null : 'The database failed job uses an unexpected connection.',
            $job->queue === QueueOperationsFailureCanaryJob::Queue ? null : 'The database failed job uses an unexpected queue.',
            $this->payloadMatches((string) $job->payload, $canaryId) ? null : 'The database failed-job payload does not match the requested synthetic canary.',
        ]));
    }

    /** @return list<string> */
    private function validateHorizonJob(?object $job, string $canaryId): array
    {
        if ($job === null) {
            return [];
        }

        return array_values(array_filter([
            ($job->connection ?? null) === QueueOperationsFailureCanaryJob::Connection ? null : 'The Horizon failed job uses an unexpected connection.',
            ($job->queue ?? null) === QueueOperationsFailureCanaryJob::Queue ? null : 'The Horizon failed job uses an unexpected queue.',
            ($job->name ?? null) === QueueOperationsFailureCanaryJob::DisplayName ? null : 'The Horizon failed job uses an unexpected display name.',
            $this->payloadMatches((string) ($job->payload ?? ''), $canaryId) ? null : 'The Horizon failed-job payload does not match the requested synthetic canary.',
        ]));
    }

    private function payloadMatches(string $payload, string $canaryId): bool
    {
        $decoded = json_decode($payload, true);

        if (! is_array($decoded)
            || ($decoded['displayName'] ?? null) !== QueueOperationsFailureCanaryJob::DisplayName
            || ($decoded['data']['commandName'] ?? null) !== QueueOperationsFailureCanaryJob::class
            || ! is_string($decoded['data']['command'] ?? null)) {
            return false;
        }

        $job = @unserialize($decoded['data']['command'], [
            'allowed_classes' => [QueueOperationsFailureCanaryJob::class],
        ]);

        return $job instanceof QueueOperationsFailureCanaryJob
            && hash_equals($canaryId, $job->canaryId);
    }

    private function deleteDatabaseJob(
        ConnectionInterface $connection,
        string $table,
        ?object $job,
        string $jobId,
    ): int {
        if ($job === null) {
            return 0;
        }

        return $connection->table($table)
            ->where('id', $job->id)
            ->where('uuid', $jobId)
            ->where('connection', QueueOperationsFailureCanaryJob::Connection)
            ->where('queue', QueueOperationsFailureCanaryJob::Queue)
            ->delete();
    }

    /**
     * @param  list<string>  $errors
     * @param  array<string, int>  $counts
     */
    private function respond(
        string $status,
        string $jobId,
        string $canaryId,
        array $errors = [],
        array $counts = [],
        int $exitCode = self::FAILURE,
    ): int {
        $payload = array_merge([
            'status' => $status,
            'job_id' => $jobId,
            'canary_id' => $canaryId,
        ], $counts);

        if ($errors !== []) {
            $payload['errors'] = $errors;
        }

        if ($this->option('json')) {
            $this->line(json_encode($payload, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
        } elseif ($exitCode === self::SUCCESS) {
            $this->components->info("Synthetic failure canary [{$canaryId}] is clean.");
        } else {
            foreach ($errors as $error) {
                $this->components->error($error);
            }
        }

        return $exitCode;
    }
}
