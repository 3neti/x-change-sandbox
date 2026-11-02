<?php

declare(strict_types=1);
use App\Providers\AppServiceProvider;
use Tests\TestCase;

uses(TestCase::class);

it('configures a private shared disk for envelope documents', function (): void {
    expect(config('filesystems.disks.envelope-private.driver'))->toBe('s3')
        ->and(config('filesystems.disks.envelope-private.visibility'))->toBe('private')
        ->and(config('filesystems.disks.envelope-private.throw'))->toBeTrue();
});

it('inherits cloud managed storage settings without changing the source disk', function (): void {
    $source = ['driver' => 's3', 'region' => 'auto', 'bucket' => 'managed-bucket', 'visibility' => 'public'];
    config()->set('filesystems.disks.s3', $source);

    (new AppServiceProvider(app()))->register();

    expect(config('filesystems.disks.s3'))->toBe($source)
        ->and(config('filesystems.disks.envelope-private'))->toMatchArray([
            'driver' => 's3', 'region' => 'auto', 'bucket' => 'managed-bucket',
            'visibility' => 'private', 'throw' => true, 'report' => true,
        ]);
});
