<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;

it('reports installed manifests without mutating queue operations', function (): void {
    $exitCode = Artisan::call('settlement-os:queues:discover', ['--json' => true]);
    $payload = json_decode(Artisan::output(), true, flags: JSON_THROW_ON_ERROR);

    expect($exitCode)->toBe(0)
        ->and($payload)->toBeArray();
});

it('fails strict inspection when Horizon is enabled without Redis commissioning', function (): void {
    config()->set('queue-operations.horizon_enabled', true);
    config()->set('queue-operations.authorized_queues', []);
    config()->set('queue.default', 'database');
    config()->set('queue-operations.connection', 'database');

    $exitCode = Artisan::call('settlement-os:queues:inspect', [
        '--strict' => true,
        '--json' => true,
    ]);
    $payload = json_decode(Artisan::output(), true, flags: JSON_THROW_ON_ERROR);

    expect($exitCode)->toBe(1)
        ->and($payload['ready'])->toBeFalse()
        ->and($payload['errors'])->toContain(
            'Horizon supervisors require HORIZON_QUEUE_CONNECTION=redis.',
            'Horizon is enabled without any explicitly authorized queues.',
        );
});
