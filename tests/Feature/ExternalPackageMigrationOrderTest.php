<?php

declare(strict_types=1);

use Composer\InstalledVersions;

it('loads emi funding evidence migrations before x-change funding tables', function (): void {
    $migrator = app('migrator');
    $migrationNames = array_keys($migrator->getMigrationFiles($migrator->paths()));

    expect(InstalledVersions::getPrettyVersion('3neti/emi-core'))
        ->toBe('v2.0.0-beta.5')
        ->and(InstalledVersions::getPrettyVersion('3neti/x-change'))
        ->toBe('v1.0.0-beta.51')
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

it('uses PostgreSQL-safe funding transfer match constraints', function (): void {
    $migration = file_get_contents(
        base_path('vendor/3neti/x-change/database/migrations/2026_07_27_103900_create_x_change_funding_request_transfer_matches_table.php'),
    );

    expect($migration)->not->toBeFalse()
        ->toContain(
            'xfrtm_funding_request_unique',
            'xfrtm_funding_request_foreign',
            'xfrtm_observation_unique',
            'xfrtm_observation_foreign',
        );
});

it('uses PostgreSQL-safe funding transfer amount reservation constraints', function (): void {
    $migration = file_get_contents(
        base_path('vendor/3neti/x-change/database/migrations/2026_07_27_121216_create_x_change_funding_transfer_amount_reservations_table.php'),
    );

    expect($migration)->not->toBeFalse()
        ->toContain(
            'xftram_funding_request_unique',
            'xftram_funding_request_foreign',
        );
});

it('uses package-owned branding on the host authentication surface', function (): void {
    $component = file_get_contents(
        resource_path('js/components/AppLogoIcon.vue'),
    );
    $packageStub = file_get_contents(
        base_path('vendor/3neti/x-change/stubs/resources/js/components/AppLogoIcon.vue.stub'),
    );

    expect($component)->not->toBeFalse()
        ->toContain('/vendor/x-change/images/logo-orange.png')
        ->toContain('/vendor/x-change/images/logo-silver.png')
        ->and($packageStub)->not->toBeFalse()
        ->toContain('/vendor/x-change/images/logo-orange.png')
        ->toContain('/vendor/x-change/images/logo-silver.png');
});
