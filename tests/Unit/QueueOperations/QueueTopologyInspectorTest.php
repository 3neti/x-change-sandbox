<?php

declare(strict_types=1);

use App\Support\QueueOperations\InstalledQueueManifestDiscovery;
use App\Support\QueueOperations\QueueTopologyInspector;
use Tests\TestCase;

uses(TestCase::class);

it('fails closed when Horizon is enabled without Redis or authorized queues', function (): void {
    config()->set('queue-operations.horizon_enabled', true);
    config()->set('queue-operations.authorized_queues', []);
    config()->set('queue.default', 'database');
    config()->set('queue-operations.connection', 'database');

    $report = (new QueueTopologyInspector(
        new InstalledQueueManifestDiscovery([]),
    ))->inspect();

    expect($report['ready'])->toBeFalse()
        ->and($report['errors'])->toContain(
            'Horizon supervisors require HORIZON_QUEUE_CONNECTION=redis.',
            'Horizon is enabled without any explicitly authorized queues.',
        );
});

it('rejects unknown authorization and financial use of the default queue', function (): void {
    $root = sys_get_temp_dir().'/queue-inspection-'.str()->uuid();
    mkdir($root.'/resources/settlement-os', recursive: true);
    file_put_contents($root.'/composer.json', json_encode([
        'name' => 'example/financial-package',
        'extra' => [
            'settlement-os' => [
                'queue-manifest' => 'resources/settlement-os/queues.php',
            ],
        ],
    ], JSON_THROW_ON_ERROR));
    file_put_contents($root.'/resources/settlement-os/queues.php', <<<'PHP'
<?php
return [
    'schema' => 'settlement-os.queue-topology.v1',
    'package' => 'example/financial-package',
    'lanes' => [
        'money' => ['queue' => 'default', 'criticality' => 'financial'],
    ],
];
PHP);
    config()->set('queue-operations.horizon_enabled', false);
    config()->set('queue-operations.authorized_queues', ['unknown-queue']);

    $report = (new QueueTopologyInspector(
        new InstalledQueueManifestDiscovery(['example/financial-package' => $root]),
    ))->inspect();

    expect($report['ready'])->toBeFalse()
        ->and($report['errors'])->toContain(
            'example/financial-package:money is financial and may not use default.',
            'Authorized queue [unknown-queue] is not declared by an installed package.',
        );
});

it('keeps uncommissioned financial lanes disabled while accepting an authorized planning lane', function (): void {
    $root = sys_get_temp_dir().'/queue-timing-'.str()->uuid();
    mkdir($root.'/resources/settlement-os', recursive: true);
    file_put_contents($root.'/composer.json', json_encode([
        'name' => 'example/financial-package',
        'extra' => [
            'settlement-os' => [
                'queue-manifest' => 'resources/settlement-os/queues.php',
            ],
        ],
    ], JSON_THROW_ON_ERROR));
    file_put_contents($root.'/resources/settlement-os/queues.php', <<<'PHP'
<?php
return [
    'schema' => 'settlement-os.queue-topology.v1',
    'package' => 'example/financial-package',
    'lanes' => [
        'money' => [
            'queue' => 'example-money',
            'criticality' => 'financial',
            'recommended' => ['timeout' => 90],
        ],
        'planning' => [
            'queue' => 'example-planning',
            'criticality' => 'planning',
            'recommended' => ['timeout' => 60],
        ],
    ],
];
PHP);
    config()->set('queue-operations.horizon_enabled', true);
    config()->set('queue-operations.authorized_queues', ['example-planning']);
    config()->set('queue.default', 'database');
    config()->set('queue-operations.connection', 'redis');
    config()->set('queue.connections.redis.retry_after', 90);

    $discovery = new InstalledQueueManifestDiscovery(['example/financial-package' => $root]);
    $planningOnly = (new QueueTopologyInspector($discovery))->inspect();

    expect($planningOnly['ready'])->toBeTrue()
        ->and($planningOnly['queue_connection'])->toBe('database')
        ->and($planningOnly['horizon_connection'])->toBe('redis')
        ->and($planningOnly['errors'])->toBeEmpty()
        ->and($planningOnly['warnings'])->toContain(
            'Financial queue [example-money] is declared but not commissioned.',
        );

    config()->set('queue-operations.authorized_queues', ['example-money']);

    $unsafeTiming = (new QueueTopologyInspector($discovery))->inspect();

    expect($unsafeTiming['ready'])->toBeFalse()
        ->and($unsafeTiming['errors'])->toContain(
            'example/financial-package:money timeout must be lower than [redis] retry_after.',
        );
});

it('rejects conflicting shared queue semantics and excessive concurrency', function (): void {
    $roots = [];

    foreach ([
        'example/first' => ['financial', 'subject-serialized', 5],
        'example/second' => ['communication', 'independent', 2],
    ] as $package => [$criticality, $ordering, $maxProcesses]) {
        $root = sys_get_temp_dir().'/queue-conflict-'.str()->uuid();
        mkdir($root.'/resources/settlement-os', recursive: true);
        file_put_contents($root.'/composer.json', json_encode([
            'name' => $package,
            'extra' => ['settlement-os' => ['queue-manifest' => 'resources/settlement-os/queues.php']],
        ], JSON_THROW_ON_ERROR));
        file_put_contents($root.'/resources/settlement-os/queues.php', sprintf(<<<'PHP'
<?php
return [
    'schema' => 'settlement-os.queue-topology.v1',
    'package' => '%s',
    'lanes' => [
        'shared' => [
            'queue' => 'shared-lane',
            'criticality' => '%s',
            'ordering' => '%s',
            'explicit_commissioning' => true,
            'recommended' => ['timeout' => 60, 'max_processes' => %d],
        ],
    ],
];
PHP, $package, $criticality, $ordering, $maxProcesses));
        $roots[$package] = $root;
    }

    config()->set('queue-operations.horizon_enabled', true);
    config()->set('queue-operations.authorized_queues', ['shared-lane']);
    config()->set('queue-operations.max_processes_ceiling', 4);
    config()->set('queue.default', 'redis');
    config()->set('queue.connections.redis.retry_after', 90);

    $report = (new QueueTopologyInspector(new InstalledQueueManifestDiscovery($roots)))->inspect();

    expect($report['ready'])->toBeFalse()
        ->and($report['errors'])->toContain(
            'Queue [shared-lane] is declared with incompatible semantics.',
            'example/first:shared exceeds the approved process ceiling [4].',
        );
});
