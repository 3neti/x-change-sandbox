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
            ->waitForText('Quick Generate Runtime', 10)
            ->assertPathIs('/x/cockpit/quick-generate')
            ->assertSee('Submit through existing issuance handoff')
            ->assertSee('Submit will call the existing x-change issuance handoff route.')
            ->assertSee('Existing issuance handoff')
            ->assertSee('Quick Generate now submits through the approved Cockpit mutation route')
            ->assertSee('Use Quick Generate form above')
            ->assertSee('Use the Quick Generate form')
            ->assertSee('Pricing and funding preflights appear after a successful form submit.')
            ->assertDontSee('No Cockpit mutation route is registered')
            ->assertDontSee('Quick Generate mutation remains explicitly unauthorized')
            ->assertDontSee('No voucher generation')
            ->assertDontSee('No wallet debit or reservation')
            ->assertDontSee('No journal or feedback side effect');
    });
});
