<?php

declare(strict_types=1);

use App\Jobs\QueueOperationsFailureCanaryJob;
use Illuminate\Contracts\Redis\Connection;
use Illuminate\Contracts\Redis\Factory as RedisFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Horizon\Contracts\JobRepository;

uses(RefreshDatabase::class);

function syntheticFailurePayload(string $canaryId): string
{
    return json_encode([
        'uuid' => (string) Str::uuid(),
        'displayName' => QueueOperationsFailureCanaryJob::DisplayName,
        'data' => [
            'commandName' => QueueOperationsFailureCanaryJob::class,
            'command' => serialize(new QueueOperationsFailureCanaryJob($canaryId)),
        ],
    ], JSON_THROW_ON_ERROR);
}

function horizonFailure(string $jobId, string $canaryId): object
{
    return (object) [
        'id' => $jobId,
        'connection' => QueueOperationsFailureCanaryJob::Connection,
        'queue' => QueueOperationsFailureCanaryJob::Queue,
        'name' => QueueOperationsFailureCanaryJob::DisplayName,
        'status' => 'failed',
        'payload' => syntheticFailurePayload($canaryId),
    ];
}

function mockFailureMarkerCleanup(string $canaryId, int $deleted = 2): void
{
    $connection = Mockery::mock(Connection::class);
    $connection->shouldReceive('command')->once()->with('del', [
        QueueOperationsFailureCanaryJob::markerKey($canaryId),
        QueueOperationsFailureCanaryJob::attemptsKey($canaryId),
    ])->andReturn($deleted);

    $redis = Mockery::mock(RedisFactory::class);
    $redis->shouldReceive('connection')->once()->andReturn($connection);
    app()->instance(RedisFactory::class, $redis);
}

function insertSyntheticFailure(string $jobId, string $canaryId, string $queue = QueueOperationsFailureCanaryJob::Queue): void
{
    DB::table('failed_jobs')->insert([
        'uuid' => $jobId,
        'connection' => QueueOperationsFailureCanaryJob::Connection,
        'queue' => $queue,
        'payload' => syntheticFailurePayload($canaryId),
        'exception' => 'Sanitized synthetic failure.',
        'failed_at' => now(),
    ]);
}

it('removes one exact synthetic failure from database Horizon and marker storage', function (): void {
    $jobId = (string) Str::uuid();
    $canaryId = (string) Str::ulid();
    insertSyntheticFailure($jobId, $canaryId);
    mockFailureMarkerCleanup($canaryId);

    $jobs = Mockery::mock(JobRepository::class);
    $jobs->shouldReceive('findFailed')->twice()->with($jobId)->andReturn(horizonFailure($jobId, $canaryId), null);
    $jobs->shouldReceive('deleteFailed')->once()->with($jobId)->andReturn(1);
    app()->instance(JobRepository::class, $jobs);

    $exitCode = Artisan::call('settlement-os:queues:cleanup-failure', [
        'jobId' => $jobId,
        'canaryId' => $canaryId,
        '--json' => true,
    ]);
    $result = json_decode(Artisan::output(), true, flags: JSON_THROW_ON_ERROR);

    expect($exitCode)->toBe(0)
        ->and($result)->toMatchArray([
            'status' => 'cleaned',
            'job_id' => $jobId,
            'canary_id' => $canaryId,
            'database_records_deleted' => 1,
            'horizon_records_deleted' => 1,
            'marker_keys_deleted' => 2,
        ])
        ->and(DB::table('failed_jobs')->where('uuid', $jobId)->exists())->toBeFalse();
});

it('recovers when only the database failed-job record remains', function (): void {
    $jobId = (string) Str::uuid();
    $canaryId = (string) Str::ulid();
    insertSyntheticFailure($jobId, $canaryId);
    mockFailureMarkerCleanup($canaryId, 0);

    $jobs = Mockery::mock(JobRepository::class);
    $jobs->shouldReceive('findFailed')->twice()->with($jobId)->andReturn(null);
    $jobs->shouldNotReceive('deleteFailed');
    app()->instance(JobRepository::class, $jobs);

    $exitCode = Artisan::call('settlement-os:queues:cleanup-failure', [
        'jobId' => $jobId,
        'canaryId' => $canaryId,
        '--json' => true,
    ]);
    $result = json_decode(Artisan::output(), true, flags: JSON_THROW_ON_ERROR);

    expect($exitCode)->toBe(0)
        ->and($result['status'])->toBe('cleaned')
        ->and($result['database_records_deleted'])->toBe(1)
        ->and($result['horizon_records_deleted'])->toBe(0);
});

it('recovers when only the Horizon failed-job record remains', function (): void {
    $jobId = (string) Str::uuid();
    $canaryId = (string) Str::ulid();
    mockFailureMarkerCleanup($canaryId, 1);

    $jobs = Mockery::mock(JobRepository::class);
    $jobs->shouldReceive('findFailed')->twice()->with($jobId)->andReturn(horizonFailure($jobId, $canaryId), null);
    $jobs->shouldReceive('deleteFailed')->once()->with($jobId)->andReturn(1);
    app()->instance(JobRepository::class, $jobs);

    $exitCode = Artisan::call('settlement-os:queues:cleanup-failure', [
        'jobId' => $jobId,
        'canaryId' => $canaryId,
        '--json' => true,
    ]);
    $result = json_decode(Artisan::output(), true, flags: JSON_THROW_ON_ERROR);

    expect($exitCode)->toBe(0)
        ->and($result['status'])->toBe('cleaned')
        ->and($result['database_records_deleted'])->toBe(0)
        ->and($result['horizon_records_deleted'])->toBe(1);
});

it('is an idempotent no-op after all exact failure state is gone', function (): void {
    $jobId = (string) Str::uuid();
    $canaryId = (string) Str::ulid();
    mockFailureMarkerCleanup($canaryId, 0);

    $jobs = Mockery::mock(JobRepository::class);
    $jobs->shouldReceive('findFailed')->twice()->with($jobId)->andReturn(null);
    $jobs->shouldNotReceive('deleteFailed');
    app()->instance(JobRepository::class, $jobs);

    $exitCode = Artisan::call('settlement-os:queues:cleanup-failure', [
        'jobId' => $jobId,
        'canaryId' => $canaryId,
        '--json' => true,
    ]);
    $result = json_decode(Artisan::output(), true, flags: JSON_THROW_ON_ERROR);

    expect($exitCode)->toBe(0)
        ->and($result['status'])->toBe('already_clean')
        ->and($result['database_records_deleted'])->toBe(0)
        ->and($result['horizon_records_deleted'])->toBe(0)
        ->and($result['marker_keys_deleted'])->toBe(0);
});

it('fails closed when a database record does not match the synthetic campaigns canary', function (): void {
    $jobId = (string) Str::uuid();
    $canaryId = (string) Str::ulid();
    insertSyntheticFailure($jobId, $canaryId, 'x-change-funding');

    $jobs = Mockery::mock(JobRepository::class);
    $jobs->shouldReceive('findFailed')->once()->with($jobId)->andReturn(null);
    $jobs->shouldNotReceive('deleteFailed');
    app()->instance(JobRepository::class, $jobs);

    $redis = Mockery::mock(RedisFactory::class);
    $redis->shouldNotReceive('connection');
    app()->instance(RedisFactory::class, $redis);

    $exitCode = Artisan::call('settlement-os:queues:cleanup-failure', [
        'jobId' => $jobId,
        'canaryId' => $canaryId,
        '--json' => true,
    ]);
    $result = json_decode(Artisan::output(), true, flags: JSON_THROW_ON_ERROR);

    expect($exitCode)->toBe(1)
        ->and($result['status'])->toBe('blocked')
        ->and($result['errors'])->toContain('The database failed job uses an unexpected queue.')
        ->and(DB::table('failed_jobs')->where('uuid', $jobId)->exists())->toBeTrue();
});

it('rejects invalid identifiers before touching any storage', function (): void {
    $jobs = Mockery::mock(JobRepository::class);
    $jobs->shouldNotReceive('findFailed');
    app()->instance(JobRepository::class, $jobs);

    $redis = Mockery::mock(RedisFactory::class);
    $redis->shouldNotReceive('connection');
    app()->instance(RedisFactory::class, $redis);

    $exitCode = Artisan::call('settlement-os:queues:cleanup-failure', [
        'jobId' => 'not-a-uuid',
        'canaryId' => 'not-a-ulid',
        '--json' => true,
    ]);
    $result = json_decode(Artisan::output(), true, flags: JSON_THROW_ON_ERROR);

    expect($exitCode)->toBe(1)
        ->and($result['status'])->toBe('blocked')
        ->and($result['errors'])->toContain(
            'The job ID must be a UUID.',
            'The canary ID must be a ULID.',
        );
});
