<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;

test('cockpit runtime profile diagnostics page renders read-only operator acceptance facts', function () {
    $user = User::query()->where('email', 'dusk-cockpit@example.test')->first();

    if (! $user instanceof User) {
        $user = User::factory()->create([
            'name' => 'Dusk Cockpit Operator',
            'email' => 'dusk-cockpit@example.test',
            'mobile' => '639170000001',
            'password' => Hash::make('password'),
        ]);
    }

    $this->browse(function (Browser $browser) use ($user): void {
        $browser
            ->loginAs($user)
            ->visit('/x/cockpit/diagnostics/runtime-profile')
            ->waitForText('Operator Activity Runtime Profile', 10)
            ->assertPathIs('/x/cockpit/diagnostics/runtime-profile')
            ->assertSee('Runtime Profile')
            ->assertSee('RUNTIME STATUS')
            ->assertSee('RUNTIME COMPONENTS')
            ->assertSee('Explicit configuration and fallbacks')
            ->assertSee('repository')
            ->assertSee('recorder')
            ->assertSee('journal_handoff')
            ->assertSee('action_handoff')
            ->assertSee('feedback_handoff')
            ->assertSee('This diagnostics surface is read-only')
            ->assertSee('Runtime capabilities remain explicit opt-in')
            ->assertSee('moves_money')
            ->assertSee('no')
            ->assertDontSee('Enable handoffs')
            ->assertDontSee('Save configuration')
            ->assertDontSee('provider_payload')
            ->assertDontSee('raw_payload')
            ->assertDontSee('wallet_data');
    });
});
