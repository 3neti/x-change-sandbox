<?php

declare(strict_types=1);

use Composer\InstalledVersions;

it('loads emi funding evidence migrations before x-change funding tables', function (): void {
    $migrator = app('migrator');
    $migrationNames = array_keys($migrator->getMigrationFiles($migrator->paths()));

    expect(InstalledVersions::getPrettyVersion('3neti/emi-core'))
        ->toBe('v2.0.0-beta.5')
        ->and($migrationNames)
        ->toContain(
            '2025_01_01_000008_create_webhook_receipts_table',
            '2026_07_23_085518_create_provider_funding_observations_table',
            '2026_07_23_085520_harden_emi_webhook_receipts_for_funding_evidence',
            '2026_07_23_091000_create_x_change_funding_settlements_table',
        );

    expect(array_search(
        '2026_07_23_085518_create_provider_funding_observations_table',
        $migrationNames,
        true,
    ))->toBeLessThan(array_search(
        '2026_07_23_091000_create_x_change_funding_settlements_table',
        $migrationNames,
        true,
    ));
});
