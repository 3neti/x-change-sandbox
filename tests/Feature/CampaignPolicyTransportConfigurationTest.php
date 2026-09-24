<?php

use App\Providers\AppServiceProvider;
use LBHurtado\XChange\Services\Settlement\PolicyCompletionTransportDispositionCatalog;

it('registers only an enabled demo disposition and preserves other transports', function (): void {
    config()->set('x-change.settlement.policy_completion.transports', ['other-driver@1' => ['preserved' => true]]);
    config()->set('campaign-policy.enabled', false);
    (new AppServiceProvider(app()))->boot();
    expect(config('x-change.settlement.policy_completion.transports'))->toHaveCount(1);
    config()->set('campaign-policy.enabled', true);
    config()->set('campaign-policy.disposition.submission_endpoint', 'https://demo.m.pipedream.net/policy');
    (new AppServiceProvider(app()))->boot();
    $disposition = app(PolicyCompletionTransportDispositionCatalog::class)->for('aui.personal-accident.provisional-cover', '1.0.0');
    expect($disposition->provider())->toBe('pipedream-test')
        ->and($disposition->credentialReference())->toBe('services.pipedream.policy_completion_token')
        ->and(config('x-change.settlement.policy_completion.transports'))->toHaveCount(2)
        ->and(config('x-change.settlement.policy_completion.transports')['other-driver@1'])->toBe(['preserved' => true]);
});
