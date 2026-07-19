<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;

test('cockpit quick generate renders the reconciled runtime copy', function () {
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
            ->visit('/x/cockpit/quick-generate')
            ->waitForText('Quick Generate', 10)
            ->assertPathIs('/x/cockpit/quick-generate')
            ->assertPresent('[data-testid="cockpit-quick-generate-shell"]')
            ->assertPresent('[data-testid="cockpit-quick-generate-draft-contract-panel"]')
            ->assertPresent('[data-testid="cockpit-quick-generate-diagnostics-summary"]')
            ->assertSee('PAY CODE GENERATION')
            ->assertSee('Create a Pay Code through the approved template-first handoff')
            ->assertSee('Design the Pay Code contract before generation')
            ->assertSee('Generate Pay Code')
            ->assertDontSee('No Cockpit mutation route is registered')
            ->assertDontSee('Quick Generate mutation remains explicitly unauthorized')
            ->assertDontSee('No voucher generation')
            ->assertDontSee('No wallet debit or reservation')
            ->assertDontSee('No journal or feedback side effect');
    });
});
