<?php

declare(strict_types=1);

use App\Jobs\QueueOperationsCanaryJob;
use Illuminate\Contracts\Redis\Connection;
use Illuminate\Contracts\Redis\Factory as RedisFactory;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;

it('blocks a canary while Horizon is disabled', function (): void {
    config()->set('queue-operations.horizon_enabled', false);
    config()->set('queue-operations.authorized_queues', []);
    config()->set('queue.default', 'database');

    $exitCode = Artisan::call('settlement-os:queues:canary', ['--json' => true]);
    $payload = json_decode(Artisan::output(), true, flags: JSON_THROW_ON_ERROR);

    expect($exitCode)->toBe(1)
        ->and($payload['status'])->toBe('blocked')
        ->and($payload['errors'])->toContain(
            'Horizon is not enabled for this process.',
            'Queue [campaigns] is not explicitly authorized.',
        );
});

it('dispatches and observes one explicitly authorized planning canary', function (): void {
    Queue::fake();

    config()->set('queue-operations.horizon_enabled', true);
    config()->set('queue-operations.authorized_queues', ['campaigns']);
    config()->set('queue.default', 'database');
    config()->set('queue-operations.connection', 'redis');

    $connection = Mockery::mock(Connection::class);
    $connection->shouldReceive('command')->once()->with('ping')->andReturn(true);
    $connection->shouldReceive('command')->once()->with('del', Mockery::on(
        fn (array $arguments): bool => count($arguments) === 1
            && str_starts_with($arguments[0], 'settlement-os:queue-canary:'),
    ))->andReturn(1);
    $connection->shouldReceive('command')->once()->with('get', Mockery::on(
        fn (array $arguments): bool => count($arguments) === 1
            && str_starts_with($arguments[0], 'settlement-os:queue-canary:'),
    ))->andReturn(json_encode(['status' => 'completed'], JSON_THROW_ON_ERROR));

    $redis = Mockery::mock(RedisFactory::class);
    $redis->shouldReceive('connection')->andReturn($connection);
    app()->instance(RedisFactory::class, $redis);

    $exitCode = Artisan::call('settlement-os:queues:canary', [
        '--queue' => 'campaigns',
        '--wait' => 1,
        '--json' => true,
    ]);
    $payload = json_decode(Artisan::output(), true, flags: JSON_THROW_ON_ERROR);

    expect($exitCode)->toBe(0)
        ->and($payload['status'])->toBe('completed')
        ->and($payload['queue'])->toBe('campaigns');

    Queue::assertPushedOn('campaigns', QueueOperationsCanaryJob::class);
});

it('refuses to use a financial lane as a canary', function (): void {
    config()->set('queue-operations.horizon_enabled', true);
    config()->set('queue-operations.authorized_queues', ['x-change-funding']);
    config()->set('queue.default', 'database');
    config()->set('queue-operations.connection', 'redis');

    $exitCode = Artisan::call('settlement-os:queues:canary', [
        '--queue' => 'x-change-funding',
        '--json' => true,
    ]);
    $payload = json_decode(Artisan::output(), true, flags: JSON_THROW_ON_ERROR);

    expect($exitCode)->toBe(1)
        ->and($payload['status'])->toBe('blocked')
        ->and($payload['errors'])->toContain(
            'Queue [x-change-funding] is not a planning-only lane.',
        );
});

it('writes only a short-lived Redis completion marker', function (): void {
    $connection = Mockery::mock(Connection::class);
    $connection->shouldReceive('command')->once()->with('setex', Mockery::on(
        function (array $arguments): bool {
            [$key, $ttl, $payload] = $arguments;
            $decoded = json_decode($payload, true, flags: JSON_THROW_ON_ERROR);

            return $key === QueueOperationsCanaryJob::markerKey('canary-123')
                && $ttl === 300
                && $decoded['canary_id'] === 'canary-123'
                && $decoded['status'] === 'completed';
        },
    ))->andReturn(true);

    $redis = Mockery::mock(RedisFactory::class);
    $redis->shouldReceive('connection')->once()->andReturn($connection);

    $job = new QueueOperationsCanaryJob('canary-123');
    $job->handle($redis);

    expect($job->displayName())->toBe('Settlement OS Queue Canary')
        ->and($job->tags())->toBe([
            'package:host',
            'lane:canary',
            'canary:canary-123',
        ]);
});
