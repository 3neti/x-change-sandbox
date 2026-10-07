<?php

declare(strict_types=1);

$authorizedQueues = array_values(array_filter(array_map(
    static fn (string $queue): string => trim($queue),
    explode(',', (string) env('HORIZON_AUTHORIZED_QUEUES', '')),
)));

return [
    'horizon_enabled' => (bool) env('HORIZON_ENABLED', false),
    'authorized_queues' => $authorizedQueues,
    'connection' => env('HORIZON_QUEUE_CONNECTION', 'redis'),
    'max_processes_ceiling' => (int) env('HORIZON_MAX_PROCESSES_CEILING', 4),
    'require_password_confirmation' => (bool) env(
        'HORIZON_REQUIRE_PASSWORD_CONFIRMATION',
        true,
    ),
];
