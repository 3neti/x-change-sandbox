<?php

declare(strict_types=1);

use App\Support\QueueOperations\InstalledQueueManifestDiscovery;

it('discovers only installed packages that advertise a valid queue manifest', function (): void {
    $root = sys_get_temp_dir().'/queue-manifest-'.str()->uuid();
    mkdir($root.'/resources/settlement-os', recursive: true);
    file_put_contents($root.'/composer.json', json_encode([
        'name' => 'example/settlement-package',
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
    'package' => 'example/settlement-package',
    'lanes' => [
        'delivery' => [
            'queue' => 'example-delivery',
            'criticality' => 'communication',
        ],
    ],
];
PHP);

    $manifests = (new InstalledQueueManifestDiscovery([
        'example/settlement-package' => $root,
    ]))->discover();

    expect($manifests)->toHaveCount(1)
        ->and($manifests[0]['package'])->toBe('example/settlement-package')
        ->and($manifests[0]['lanes']['delivery']['queue'])->toBe('example-delivery');
});
