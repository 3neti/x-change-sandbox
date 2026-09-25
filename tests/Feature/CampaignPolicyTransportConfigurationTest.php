<?php

use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Http;
use LBHurtado\SettlementEnvelope\Services\WorkflowConnectionReadiness;
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

it('maps the enabled named connection to the accepted demo transport without network requests', function (): void {
    Http::fake();
    config()->set('settlement-envelope.connections', ['other' => ['preserved' => true]]);
    config()->set('campaign-policy.enabled', true);
    config()->set('campaign-policy.token', 'synthetic-demo-token');
    config()->set('campaign-policy.disposition.submission_endpoint', 'https://demo.example.test/policy');

    (new AppServiceProvider(app()))->boot();
    (new AppServiceProvider(app()))->boot();

    $disposition = app(PolicyCompletionTransportDispositionCatalog::class)->for('aui.personal-accident.provisional-cover', '1.0.0');
    $connection = config('settlement-envelope.connections.aui-demo');

    expect($connection['base_url'])->toBe($disposition->submissionEndpoint())
        ->and($connection['connect_timeout'])->toBe($disposition->connectTimeoutSeconds())
        ->and($connection['timeout'])->toBe($disposition->responseTimeoutSeconds())
        ->and($connection['auth'])->toBe(['type' => 'bearer', 'token' => config($disposition->credentialReference())])
        ->and(app(WorkflowConnectionReadiness::class)->check('aui-demo')->configured)->toBeTrue()
        ->and(config('settlement-envelope.connections.other'))->toBe(['preserved' => true])
        ->and(config('settlement-envelope.connections'))->toHaveCount(2);
    Http::assertNothingSent();
});

it('does not create a named connection while the demo is disabled', function (): void {
    config()->set('settlement-envelope.connections', []);
    config()->set('campaign-policy.enabled', false);
    (new AppServiceProvider(app()))->boot();
    expect(config('settlement-envelope.connections'))->toBe([]);
});

it('preserves explicitly configured named connections including invalid values', function (mixed $connection): void {
    config()->set('settlement-envelope.connections', ['aui-demo' => $connection]);
    config()->set('campaign-policy.enabled', true);
    (new AppServiceProvider(app()))->boot();
    expect(config('settlement-envelope.connections'))->toBe(['aui-demo' => $connection]);
})->with([
    'explicit null' => [null],
    'explicit connection' => [['driver' => 'http', 'base_url' => 'https://explicit.example.test']],
]);

it('keeps incomplete demo credentials unready', function (string $key): void {
    config()->set('settlement-envelope.connections', []);
    config()->set('campaign-policy.enabled', true);
    config()->set('campaign-policy.token', 'synthetic-demo-token');
    config()->set('campaign-policy.disposition.submission_endpoint', 'https://demo.example.test/policy');
    config()->set($key, null);
    (new AppServiceProvider(app()))->boot();
    expect(app(WorkflowConnectionReadiness::class)->check('aui-demo')->configured)->toBeFalse();
})->with(['campaign-policy.token', 'campaign-policy.disposition.submission_endpoint']);
