<?php

declare(strict_types=1);

use LBHurtado\XRay\Data\XRayActorData;
use LBHurtado\XRay\Data\XRayContextData;
use LBHurtado\XRay\Policies\DefaultXRayDisclosurePolicy;

it('shows voucher-approved claim details to guests while keeping private details hidden', function (): void {
    $policy = app(DefaultXRayDisclosurePolicy::class);
    $context = new XRayContextData(code: 'TEST', actor: new XRayActorData(audience: 'guest'));
    $projection = [
        'status' => 'claimable',
        'amount' => '₱25.00',
        'issuer' => 'Issuer',
        'provider' => 'NetBank',
        'reference' => 'TEST',
        'service_info' => ['host_name' => 'Sandbox', 'publisher' => '3neti', 'private_key' => 'hidden'],
        'redirect_url' => 'https://example.com/private',
        'allow' => [
            'amount' => true,
            'issuer' => true,
            'provider' => true,
            'reference' => true,
            'service_info' => true,
            'redirect_url' => false,
        ],
    ];

    $allowed = $policy->evaluate($context, $projection);
    $blocked = $policy->evaluate($context, [
        ...$projection,
        'allow' => [...$projection['allow'], 'amount' => false, 'issuer' => false],
    ]);

    $allowedDisclosures = collect($allowed['disclosures'])->keyBy('key');
    $blockedKeys = collect($blocked['disclosures'])->pluck('key')->all();

    expect($allowedDisclosures->keys()->all())->toContain('amount', 'issuer', 'provider', 'reference', 'service_info')
        ->not->toContain('redirect_url')
        ->and($allowedDisclosures->get('service_info')['value'])->not->toHaveKey('private_key')
        ->and($blockedKeys)->not->toContain('amount', 'issuer');
});
