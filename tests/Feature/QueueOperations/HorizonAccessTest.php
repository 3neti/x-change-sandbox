<?php

declare(strict_types=1);

use App\Http\Middleware\EnsureHorizonEnabled;
use App\Http\Middleware\EnsureHorizonReadOnly;
use App\Models\User;
use App\Providers\QueueOperationsServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('hides Horizon while queue operations are disabled', function (): void {
    config()->set('queue-operations.horizon_enabled', false);
    config()->set('queue-operations.dashboard_enabled', false);

    $this->actingAs(User::factory()->create())
        ->withSession(['auth.password_confirmed_at' => now()->timestamp])
        ->get('/horizon')
        ->assertNotFound();
});

it('requires normal authentication and recent password confirmation', function (): void {
    config()->set('queue-operations.horizon_enabled', false);
    config()->set('queue-operations.dashboard_enabled', true);

    $this->get('/horizon')->assertRedirect(route('login'));

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/horizon')
        ->assertRedirect(route('password.confirm'));

    $this->withSession(['auth.password_confirmed_at' => now()->timestamp])
        ->get('/horizon')
        ->assertOk();
});

it('keeps the dashboard read only while Horizon workers are disabled', function (): void {
    config()->set('queue-operations.horizon_enabled', false);
    config()->set('queue-operations.dashboard_enabled', true);

    $this->actingAs(User::factory()->create())
        ->withSession(['auth.password_confirmed_at' => now()->timestamp])
        ->post('/horizon/api/jobs/retry/synthetic-job')
        ->assertForbidden();
});

it('registers the Horizon path as a pre-commissioning read-only surface only when the dashboard is enabled', function (): void {
    config()->set('queue-operations.dashboard_enabled', true);
    config()->set('horizon.path', 'operations/horizon');

    app()->getProvider(QueueOperationsServiceProvider::class)?->register();

    expect(config('x-change.commissioning.read_only_paths'))->toContain(
        'operations/horizon',
        'operations/horizon/*',
    )->and(config('x-change.commissioning.operator_access_paths'))->toContain(
        ['path' => 'login', 'methods' => ['GET', 'HEAD', 'POST']],
        ['path' => 'user/confirm-password', 'methods' => ['GET', 'HEAD', 'POST']],
        ['path' => 'two-factor-challenge', 'methods' => ['GET', 'HEAD', 'POST']],
        ['path' => 'logout', 'methods' => ['POST']],
    );
});

it('reaches Horizon authentication through commissioning while keeping writes locked', function (): void {
    config()->set('x-change.commissioning.enabled', true);
    config()->set('x-change.commissioning.enforce_during_tests', true);
    config()->set('queue-operations.horizon_enabled', false);
    config()->set('queue-operations.dashboard_enabled', true);

    app()->getProvider(QueueOperationsServiceProvider::class)?->register();

    $this->get('/horizon')->assertRedirect(route('login'));

    $this->actingAs(User::factory()->create())
        ->withSession(['auth.password_confirmed_at' => now()->timestamp])
        ->get('/horizon')
        ->assertOk();

    $this->post('/horizon/api/jobs/retry/synthetic-job')
        ->assertRedirect('/x/commissioning');
});

it('allows an operator to authenticate and confirm their password while commissioning remains locked', function (): void {
    config()->set('x-change.commissioning.enabled', true);
    config()->set('x-change.commissioning.enforce_during_tests', true);
    config()->set('queue-operations.horizon_enabled', false);
    config()->set('queue-operations.dashboard_enabled', true);

    app()->getProvider(QueueOperationsServiceProvider::class)?->register();

    $mobile = '639171234571';
    $user = User::factory()->create([
        'mobile' => $mobile,
        'password' => Hash::make('password'),
    ]);

    $this->get('/horizon')->assertRedirect(route('login'));

    $this->post(route('login.store'), [
        'mobile' => $mobile,
        'password' => 'password',
    ])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/horizon');

    $this->assertAuthenticatedAs($user);
    $this->get('/horizon')->assertRedirect(route('password.confirm'));

    $this->post(route('password.confirm.store'), [
        'password' => 'password',
    ])->assertRedirect('/horizon');

    $this->get('/horizon')->assertOk();
});

it('can omit password confirmation from a freshly loaded configuration', function (): void {
    putenv('HORIZON_REQUIRE_PASSWORD_CONFIRMATION=false');

    try {
        $configuration = require config_path('horizon.php');

        expect($configuration['middleware'])->toBe([
            'web',
            EnsureHorizonEnabled::class,
            'auth',
            EnsureHorizonReadOnly::class,
        ]);
    } finally {
        putenv('HORIZON_REQUIRE_PASSWORD_CONFIRMATION');
    }
});
