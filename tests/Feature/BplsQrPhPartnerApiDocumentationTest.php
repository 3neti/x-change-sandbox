<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('BPLS QR Ph Partner API guide is documented without persisted credentials', function (): void {
    $path = base_path('vendor/3neti/x-change/docs/partner-api/bpls-qrph-integration-guide.md');

    expect($path)->toBeFile();

    $guide = file_get_contents($path);

    expect($guide)
        ->toContain('# BPLS QR Ph Partner API Integration Guide')
        ->toContain('POST /api/partner/v1/pay-codes/{code}/payment-attempts')
        ->toContain('https://{x-change-host}/x/pay/{CODE}')
        ->toContain('pay-codes:estimate')
        ->toContain('pay-codes:issue')
        ->toContain('pay-codes:read')
        ->toContain('pay-codes:pay')
        ->toContain('--maximum-amount-minor=200000')
        ->toContain('Never persist client secrets in documentation.')
        ->not->toMatch('/XCHANGE_CLIENT_SECRET=[A-Za-z0-9]{20,}/');
});

test('Cockpit documentation hub links developers to the BPLS QR Ph guide', function (): void {
    $this->actingAs(User::factory()->create());
    $assetVersion = file_exists(public_path('build/manifest.json'))
        ? hash_file('xxh128', public_path('build/manifest.json'))
        : '';

    $this->withHeaders([
        'X-Inertia' => 'true',
        'X-Inertia-Version' => $assetVersion,
    ])
        ->get(route('x-change.cockpit.documentation'))
        ->assertSuccessful()
        ->assertJsonPath('props.documentation.sections.2.links.1.label', 'BPLS QR Ph Developer Guide')
        ->assertJsonPath('props.documentation.sections.2.links.1.href', 'https://github.com/3neti/x-change/blob/main/docs/partner-api/bpls-qrph-integration-guide.md')
        ->assertJsonMissingPath('props.documentation.credentials')
        ->assertJsonMissingPath('props.documentation.secrets');
});
