<?php

declare(strict_types=1);
use Tests\TestCase;

uses(TestCase::class);

it('configures a private shared disk for envelope documents', function (): void {
    expect(config('filesystems.disks.envelope-private.driver'))->toBe('s3')
        ->and(config('filesystems.disks.envelope-private.visibility'))->toBe('private')
        ->and(config('filesystems.disks.envelope-private.throw'))->toBeTrue();
});
