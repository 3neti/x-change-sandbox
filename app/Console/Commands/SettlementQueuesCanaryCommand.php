<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\QueueOperationsCanaryJob;
use App\Support\QueueOperations\InstalledQueueManifestDiscovery;
use App\Support\QueueOperations\QueueTopologyInspector;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\Factory as QueueFactory;
use Illuminate\Contracts\Redis\Factory as RedisFactory;
use Illuminate\Support\Str;

#[Signature('settlement-os:queues:canary {--queue=campaigns : Authorized planning lane} {--wait=15 : Seconds to wait for completion} {--json : Emit JSON}')]
#[Description('Dispatch one local Redis-only non-financial Horizon canary')]
final class SettlementQueuesCanaryCommand extends Command
{
    public function handle(
        InstalledQueueManifestDiscovery $discovery,
        QueueTopologyInspector $inspector,
        RedisFactory $redis,
        QueueFactory $queueManager,
    ): int {
        $queue = trim((string) $this->option('queue'));
        $waitSeconds = max(1, (int) $this->option('wait'));
        $report = $inspector->inspect();
        $lane = $this->findLane($discovery->discover(), $queue);

        $errors = array_values(array_filter([
            app()->environment(['local', 'testing']) ? null : 'The canary is restricted to local and testing environments.',
            $report['ready'] ? null : 'Strict queue inspection is not ready.',
            $report['horizon_enabled'] ? null : 'Horizon is not enabled for this process.',
            $report['horizon_connection'] === 'redis' ? null : 'The Horizon supervisor connection is not Redis.',
            in_array($queue, $report['authorized_queues'], true) ? null : "Queue [{$queue}] is not explicitly authorized.",
            $lane !== null ? null : "Queue [{$queue}] is not declared by an installed package.",
            ($lane['criticality'] ?? null) === 'planning' ? null : "Queue [{$queue}] is not a planning-only lane.",
        ]));

        if ($errors !== []) {
            return $this->respond([
                'status' => 'blocked',
                'queue' => $queue,
                'errors' => $errors,
            ], self::FAILURE);
        }

        $connection = $redis->connection(config('queue.connections.redis.connection'));
        $ping = $connection->command('ping');

        if (! in_array($ping, [true, 'PONG', '+PONG'], true)) {
            return $this->respond([
                'status' => 'blocked',
                'queue' => $queue,
                'errors' => ['The configured Redis connection did not respond to PING.'],
            ], self::FAILURE);
        }

        $canaryId = (string) Str::ulid();
        $markerKey = QueueOperationsCanaryJob::markerKey($canaryId);
        $connection->command('del', [$markerKey]);
        $queueManager->connection('redis')->pushOn(
            $queue,
            (new QueueOperationsCanaryJob($canaryId))->onQueue($queue),
        );

        $deadline = microtime(true) + $waitSeconds;
        $marker = null;

        do {
            $value = $connection->command('get', [$markerKey]);

            if (is_string($value)) {
                $marker = json_decode($value, true, flags: JSON_THROW_ON_ERROR);
                break;
            }

            usleep(100_000);
        } while (microtime(true) < $deadline);

        return $this->respond([
            'status' => $marker === null ? 'timed_out' : 'completed',
            'queue' => $queue,
            'canary_id' => $canaryId,
            'marker' => $marker,
        ], $marker === null ? self::FAILURE : self::SUCCESS);
    }

    /**
     * @param  list<array{lanes: array<string, array<string, mixed>>}>  $manifests
     * @return array<string, mixed>|null
     */
    private function findLane(array $manifests, string $queue): ?array
    {
        foreach ($manifests as $manifest) {
            foreach ($manifest['lanes'] as $lane) {
                if (($lane['queue'] ?? null) === $queue) {
                    return $lane;
                }
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function respond(array $payload, int $exitCode): int
    {
        if ($this->option('json')) {
            $this->line(json_encode($payload, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
        } elseif ($exitCode === self::SUCCESS) {
            $this->components->info("Canary [{$payload['canary_id']}] completed on [{$payload['queue']}].");
        } else {
            foreach (($payload['errors'] ?? ['The canary did not complete before the timeout.']) as $error) {
                $this->components->error($error);
            }
        }

        return $exitCode;
    }
}
