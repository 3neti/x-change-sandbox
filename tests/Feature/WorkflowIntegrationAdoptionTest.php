<?php

use Composer\InstalledVersions;
use Composer\Semver\Semver;
use Illuminate\Support\Facades\Http;
use LBHurtado\SettlementEnvelope\Contracts\WorkflowCatalog;
use LBHurtado\SettlementEnvelope\Data\WorkflowContext;
use LBHurtado\XChange\Services\Settlement\ReferenceWorkflowIntegrations;
use ThreeNeti\SettlementEnvelopeAui\AuiResources;
use ThreeNeti\SettlementEnvelopePhilhealth\WorkflowAssets;

it('installs the published workflow dependencies without local path overrides', function (): void {
    foreach ([
        '3neti/x-change' => '^1.0.50',
        '3neti/settlement-envelope' => '^1.3',
        '3neti/settlement-envelope-aui' => '^1.0',
        '3neti/settlement-envelope-philhealth' => '^1.0',
    ] as $name => $constraint) {
        expect(InstalledVersions::isInstalled($name))->toBeTrue()
            ->and(Semver::satisfies(InstalledVersions::getPrettyVersion($name), $constraint))->toBeTrue()
            ->and(is_link(InstalledVersions::getInstallPath($name)))->toBeFalse();
    }
    expect(is_file(AuiResources::driverPath()))->toBeTrue()
        ->and(is_file(AuiResources::requestSchemaPath()))->toBeTrue()
        ->and(is_file(WorkflowAssets::driverPath()))->toBeTrue();
});

it('keeps workflow discovery denied by default without external requests', function (): void {
    Http::preventStrayRequests();

    expect(app(WorkflowCatalog::class)->available(new WorkflowContext('test-actor', 'test-account')))->toBe([])
        ->and(app(ReferenceWorkflowIntegrations::class)->registry())->toBeObject();

    Http::assertNothingSent();
});
