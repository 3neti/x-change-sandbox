<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Redis\Factory as RedisFactory;
use Illuminate\Foundation\Queue\Queueable;

final class QueueOperationsCanaryJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly string $canaryId)
    {
        $this->onConnection('redis');
    }

    public function handle(RedisFactory $redis): void
    {
        $redis->connection(config('queue.connections.redis.connection'))
            ->command('setex', [self::markerKey($this->canaryId), 300, json_encode([
                'canary_id' => $this->canaryId,
                'status' => 'completed',
                'completed_at' => now()->toIso8601String(),
            ], JSON_THROW_ON_ERROR)]);
    }

    public function displayName(): string
    {
        return 'Settlement OS Queue Canary';
    }

    /**
     * @return list<string>
     */
    public function tags(): array
    {
        return [
            'package:host',
            'lane:canary',
            'canary:'.$this->canaryId,
        ];
    }

    public static function markerKey(string $canaryId): string
    {
        return 'settlement-os:queue-canary:'.$canaryId;
    }
}
