<?php

declare(strict_types=1);

use App\Http\Middleware\EnsureHorizonEnabled;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('hides Horizon while queue operations are disabled', function (): void {
    config()->set('queue-operations.horizon_enabled', false);

    $this->actingAs(User::factory()->create())
        ->withSession(['auth.password_confirmed_at' => now()->timestamp])
        ->get('/horizon')
        ->assertNotFound();
});

it('requires normal authentication and recent password confirmation', function (): void {
    config()->set('queue-operations.horizon_enabled', true);

    $this->get('/horizon')->assertRedirect(route('login'));

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/horizon')
        ->assertRedirect(route('password.confirm'));

    $this->withSession(['auth.password_confirmed_at' => now()->timestamp])
        ->get('/horizon')
        ->assertOk();
});

it('can omit password confirmation from a freshly loaded configuration', function (): void {
    putenv('HORIZON_REQUIRE_PASSWORD_CONFIRMATION=false');

    try {
        $configuration = require config_path('horizon.php');

        expect($configuration['middleware'])->toBe([
            'web',
            EnsureHorizonEnabled::class,
            'auth',
        ]);
    } finally {
        putenv('HORIZON_REQUIRE_PASSWORD_CONFIRMATION');
    }
});
