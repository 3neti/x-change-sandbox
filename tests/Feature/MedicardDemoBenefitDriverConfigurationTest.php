<?php

declare(strict_types=1);

use LBHurtado\SettlementEnvelope\Services\DriverService;

it('materializes the Medicard demonstration driver in the host registry', function (): void {
    $driver = app(DriverService::class)->load('medicard.demo-benefit', '1.0.0');

    expect($driver->id)->toBe('medicard.demo-benefit')
        ->and($driver->version)->toBe('1.0.0');
});
