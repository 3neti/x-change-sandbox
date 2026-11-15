<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    Cache::flush();
    Http::preventStrayRequests();

    config([
        'app.url' => 'http://x-change-sandbox.test',
        'pipedream.base_url' => 'https://api.pipedream.com',
        'pipedream.project_id' => 'proj_AVslXKY',
        'pipedream.environment' => 'production',
        'pipedream.client_id' => 'pd-client-id',
        'pipedream.client_secret' => 'pd-client-secret',
        'pipedream.oauth_scope' => 'connect:tokens:create',
        'pipedream.connect_token_scope' => 'connect:accounts:read connect:accounts:write',
        'pipedream.external_user_namespace' => 'x-change-sandbox.test',
        'pipedream.allowed_apps' => ['gmail', 'slack', 'google_sheets'],
        'pipedream.token_expires_in' => 14400,
        'pipedream.connect_timeout' => 5,
        'pipedream.timeout' => 15,
        'pipedream.allowed_origins' => [],
    ]);
});

test('pipedream connect settings page is displayed', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('pipedream.connect.edit'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/PipedreamConnect')
            ->where('projectId', 'proj_AVslXKY')
            ->where('environment', 'production')
            ->where('configured', true)
            ->missing('clientId')
            ->missing('clientSecret')
            ->where('defaultApp', 'gmail')
            ->has('suggestedApps', 3),
        );
});

test('pipedream connect link uses production project and authenticated user', function () {
    $user = User::factory()->create();

    Http::fake([
        'api.pipedream.com/v1/oauth/token' => Http::response([
            'access_token' => 'server-access-token',
            'expires_in' => 3600,
        ]),
        'api.pipedream.com/v1/connect/proj_AVslXKY/tokens' => Http::response([
            'token' => 'connect-token',
            'expires_at' => '2026-09-22T12:00:00Z',
            'connect_link_url' => 'https://pipedream.com/_static/connect.html?t=connect-token',
        ]),
    ]);

    $this->actingAs($user)
        ->post(route('pipedream.connect.link'), ['app' => 'gmail'])
        ->assertRedirect('https://pipedream.com/_static/connect.html?t=connect-token&app=gmail');

    Http::assertSent(function ($request): bool {
        return $request->url() === 'https://api.pipedream.com/v1/oauth/token'
            && $request['grant_type'] === 'client_credentials'
            && $request['client_id'] === 'pd-client-id'
            && $request['client_secret'] === 'pd-client-secret'
            && $request['scope'] === 'connect:tokens:create';
    });

    Http::assertSent(function ($request) use ($user): bool {
        return $request->url() === 'https://api.pipedream.com/v1/connect/proj_AVslXKY/tokens'
            && $request->hasHeader('x-pd-environment', 'production')
            && $request->hasHeader('Authorization', 'Bearer server-access-token')
            && $request['external_user_id'] === 'xchange_'.hash_hmac('sha256', 'x-change-sandbox.test|'.$user::class.'|'.$user->getKey(), (string) config('app.key'))
            && $request['allowed_origins'] === ['http://x-change-sandbox.test']
            && $request['expires_in'] === 14400
            && $request['scope'] === 'connect:accounts:read connect:accounts:write'
            && str_contains((string) $request['success_redirect_uri'], '/settings/integrations/pipedream?connected=1')
            && str_contains((string) $request['error_redirect_uri'], '/settings/integrations/pipedream?connected=0');
    });
});

test('pipedream connect settings require an authenticated user', function () {
    $this
        ->get(route('pipedream.connect.edit'))
        ->assertRedirect(route('login'));
});

test('pipedream connect rejects apps outside the configured allowlist', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('pipedream.connect.link'), ['app' => 'unapproved_app'])
        ->assertSessionHasErrors('app');

    Http::assertNothingSent();
});

test('pipedream connect rejects an untrusted hosted link', function () {
    $user = User::factory()->create();

    Http::fake([
        'api.pipedream.com/v1/oauth/token' => Http::response([
            'access_token' => 'server-access-token',
            'expires_in' => 3600,
        ]),
        'api.pipedream.com/v1/connect/proj_AVslXKY/tokens' => Http::response([
            'token' => 'connect-token',
            'expires_at' => '2026-09-22T12:00:00Z',
            'connect_link_url' => 'https://attacker.example/connect?t=connect-token',
        ]),
    ]);

    $this->withoutExceptionHandling();

    expect(fn () => $this->actingAs($user)
        ->post(route('pipedream.connect.link'), ['app' => 'gmail']))
        ->toThrow(RuntimeException::class, 'untrusted Connect Link URL');
});
