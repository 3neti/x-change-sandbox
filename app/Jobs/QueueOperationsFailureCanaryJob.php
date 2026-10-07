<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Redis\Factory as RedisFactory;
use Illuminate\Foundation\Queue\Queueable;
use RuntimeException;
use Throwable;

final class QueueOperationsFailureCanaryJob implements ShouldQueue
{
    use Queueable;

    public const string Connection = 'redis';

    public const string DisplayName = 'Settlement OS Queue Failure Canary';

    public const string Queue = 'campaigns';

    public int $tries = 2;

    public function __construct(public readonly string $canaryId)
    {
        $this->onConnection(self::Connection);
    }

    public function handle(RedisFactory $redis): never
    {
        $connection = $redis->connection(config('queue.connections.redis.connection'));
        $attempt = (int) $connection->command('incr', [self::attemptsKey($this->canaryId)]);

        $connection->command('expire', [self::attemptsKey($this->canaryId), 300]);

        throw new RuntimeException("Synthetic queue failure canary attempt {$attempt}.");
    }

    public function failed(?Throwable $exception): void
    {
        $connection = app(RedisFactory::class)
            ->connection(config('queue.connections.redis.connection'));
        $attempts = (int) $connection->command('get', [self::attemptsKey($this->canaryId)]);

        $connection->command('setex', [self::markerKey($this->canaryId), 300, json_encode([
            'canary_id' => $this->canaryId,
            'status' => 'intentionally_failed',
            'attempts' => $attempts,
            'max_attempts' => $this->tries,
            'failed_at' => now()->toIso8601String(),
        ], JSON_THROW_ON_ERROR)]);
    }

    /**
     * @return list<int>
     */
    public function backoff(): array
    {
        return [1];
    }

    public function displayName(): string
    {
        return self::DisplayName;
    }

    /**
     * @return list<string>
     */
    public function tags(): array
    {
        return [
            'package:host',
            'lane:canary',
            'outcome:failure',
            'canary:'.$this->canaryId,
        ];
    }

    public static function markerKey(string $canaryId): string
    {
        return 'settlement-os:queue-failure-canary:'.$canaryId;
    }

    public static function attemptsKey(string $canaryId): string
    {
        return self::markerKey($canaryId).':attempts';
    }
}
