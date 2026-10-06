<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;

test('cockpit pay code explorer renders read-only functional parity filters', function () {
    $user = User::query()->where('email', 'dusk-cockpit-pay-code-filter@example.test')->first();

    if (! $user instanceof User) {
        $user = User::factory()->create([
            'name' => 'Dusk Cockpit Pay Code Filter Operator',
            'email' => 'dusk-cockpit-pay-code-filter@example.test',
            'mobile' => '639170000003',
            'password' => Hash::make('password'),
        ]);
    }

    $this->browse(function (Browser $browser) use ($user): void {
        $browser
            ->loginAs($user)
            ->visit('/x/cockpit/pay-codes?search=PC-DUSK-FILTER&status=redeemed')
            ->waitFor('[data-testid="cockpit-pay-code-search-input"]', 10)
            ->assertPathIs('/x/cockpit/pay-codes')
            ->assertQueryStringHas('search', 'PC-DUSK-FILTER')
            ->assertQueryStringHas('status', 'redeemed')
            ->assertSee('Search, inspect, and apply governed lifecycle controls.')
            ->assertSee('PC-DUSK-FILTER')
            ->assertSee('Redeemed')
            ->assertSee('No Pay Codes available in the sanitized read model.')
            ->assertSee('Filter metadata and read-model boundaries.')
            ->assertInputValue('search', 'PC-DUSK-FILTER')
            ->assertDontSee('Save configuration')
            ->assertDontSee('Enable handoffs')
            ->assertDontSee('provider_payload')
            ->assertDontSee('raw_payload')
            ->assertDontSee('wallet');
    });
});
