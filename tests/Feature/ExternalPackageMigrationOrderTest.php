<?php

declare(strict_types=1);

use Composer\InstalledVersions;

it('loads emi funding evidence migrations before x-change funding tables', function (): void {
    $migrator = app('migrator');
    $migrationNames = array_keys($migrator->getMigrationFiles($migrator->paths()));

    expect(InstalledVersions::getPrettyVersion('3neti/emi-core'))
        ->toBe('v2.0.0-beta.5')
        ->and(InstalledVersions::getPrettyVersion('3neti/x-change'))
        ->toBe('v1.0.0-beta.133')
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
        ->toContain("import { xChangeBrandAssets } from '@/components/x-change/brandAssets';")
        ->toContain('xChangeBrandAssets.logo')
        ->toContain('xChangeBrandAssets.light')
        ->and($packageStub)->not->toBeFalse()
        ->toContain('xChangeBrandAssets.logo')
        ->toContain('xChangeBrandAssets.light');
});

it('publishes canonical Pay Code and x-change vectors', function (): void {
    $payCodeLogo = file_get_contents(
        public_path('vendor/x-change/images/pay-code/pay-code-logo.svg'),
    );
    $sharedLogoComponent = file_get_contents(
        resource_path('js/components/x-change/PayCodeLogo.vue'),
    );

    expect($payCodeLogo)->not->toBeFalse()
        ->toContain('viewBox="0 0 1254 1254"')
        ->toContain('id="paycode-navy"')
        ->toContain('id="paycode-red"')
        ->and($sharedLogoComponent)->not->toBeFalse()
        ->toContain('/vendor/x-change/images/pay-code/pay-code-logo.svg')
        ->toContain('/vendor/x-change/images/pay-code/pay-code-mark.svg')
        ->and(public_path('vendor/x-change/images/brand-library/inventory.json'))->toBeFile()
        ->and(public_path('vendor/x-change/images/brand-library/x-change/svg/x-change-logo.svg'))->toBeFile()
        ->and(public_path('vendor/x-change/images/brand-library/g-clef-pulley/svg/g-clef-pulley-logo.svg'))->toBeFile();
});

it('uses the package-owned x-change landing page and safe product presentation', function (): void {
    $page = file_get_contents(resource_path('js/pages/Welcome.vue'));
    $packageStub = file_get_contents(
        base_path('vendor/3neti/x-change/stubs/resources/js/pages/Welcome.vue.stub'),
    );
    $claimPresentation = file_get_contents(
        resource_path('js/cockpit/components/CockpitLandingClaimExperiencePresentation.vue'),
    );

    expect($page)->not->toBeFalse()
        ->toContain('Cashless disbursements')
        ->toContain('Money should adapt to people.')
        ->toContain('Receive a Pay Code. Send to the account you choose.')
        ->toContain('Claim when you’re ready—with a participating bank or')
        ->toContain('Claim Pay Code')
        ->toContain(':href="startClaim()"')
        ->toContain('gClefPulleyBrandAssets.logo')
        ->toContain('bg-[length:auto_100%]')
        ->toContain('opacity-[0.1]')
        ->toContain('{{ $page.props.name }}')
        ->toContain('Powered by x-change')
        ->toContain('3neti/x-change {{ page.props.xchange.version }}')
        ->not->toContain('$page.props.version')
        ->toContain('© 2026 3neti R&amp;D OPC')
        ->toContain('mx-auto flex h-24 w-full max-w-[88rem]')
        ->toContain('mx-auto grid w-full max-w-[88rem]')
        ->toContain("import XChangeLogo from '@/components/x-change/XChangeLogo.vue'")
        ->toContain('<XChangeLogo')
        ->toContain('sm:h-14')
        ->toContain('amount="₱537.00"')
        ->toContain('estimated-cost="₱543.90"')
        ->toContain('Open Cockpit')
        ->toContain('DRAFT')
        ->toContain('the instruction')
        ->toContain('ISSUE')
        ->toContain('the Pay Code')
        ->toContain('CLAIM')
        ->toContain('the payout')
        ->toContain('CockpitQuickGenerateOrderPresentation')
        ->toContain('CockpitLandingClaimExperiencePresentation')
        ->not->toContain('Pay Code disbursements')
        ->not->toContain('Create a controlled payout')
        ->not->toContain('Funds stay with your regulated bank or EMI provider.')
        ->not->toContain("import PayCodeLogo from '@/components/x-change/PayCodeLogo.vue'")
        ->not->toContain('Create the order')
        ->not->toContain('Issue the Pay Code')
        ->not->toContain('Recipient claims')
        ->not->toContain('/vendor/x-change/images/landing/cockpit-overview.png')
        ->and($packageStub)->not->toBeFalse()
        ->toContain('Money should adapt to people.')
        ->toContain('CockpitLandingClaimExperiencePresentation')
        ->and($claimPresentation)->not->toBeFalse()
        ->toContain("code: 'AA-317'")
        ->toContain("amount: '₱537.00'")
        ->toContain("default: '537'")
        ->not->toContain('DEMO-500')
        ->not->toContain('₱500.00')
        ->and(resource_path('js/cockpit/components/CockpitQuickGenerateOrderPresentation.vue'))->toBeFile()
        ->and(resource_path('js/cockpit/components/CockpitLandingClaimExperiencePresentation.vue'))->toBeFile();
});

it('uses the canonical package favicon in the host application shell', function (): void {
    $shell = file_get_contents(resource_path('views/app.blade.php'));
    $favicon = file_get_contents(public_path('vendor/x-change/favicon.svg'));

    expect($shell)->not->toBeFalse()
        ->toContain('<link rel="icon" href="/vendor/x-change/favicon.svg" type="image/svg+xml">')
        ->not->toContain('<link rel="icon" href="/favicon.svg" type="image/svg+xml">')
        ->and($favicon)->not->toBeFalse()
        ->toContain('<path')
        ->not->toContain('<image');
});
