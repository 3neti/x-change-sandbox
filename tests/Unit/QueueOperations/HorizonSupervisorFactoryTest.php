<?php

declare(strict_types=1);

use App\Support\QueueOperations\HorizonSupervisorFactory;

it('builds supervisors only for explicitly authorized queues', function (): void {
    $manifests = [[
        'package' => 'example/package',
        'lanes' => [
            'authorized' => [
                'queue' => 'example-authorized',
                'recommended' => [
                    'max_processes' => 2,
                    'tries' => 3,
                    'timeout' => 45,
                ],
            ],
            'new-financial-lane' => [
                'queue' => 'example-not-authorized',
                'recommended' => ['max_processes' => 1],
            ],
        ],
    ]];

    $supervisors = (new HorizonSupervisorFactory)->make(
        $manifests,
        ['example-authorized'],
        'redis',
    );

    expect($supervisors)->toHaveCount(1)
        ->and($supervisors)->toHaveKey('settlement-os-example-package-authorized')
        ->and($supervisors['settlement-os-example-package-authorized'])->toMatchArray([
            'connection' => 'redis',
            'queue' => ['example-authorized'],
            'processes' => 2,
            'maxProcesses' => 2,
            'tries' => 3,
            'timeout' => 45,
        ]);
});
