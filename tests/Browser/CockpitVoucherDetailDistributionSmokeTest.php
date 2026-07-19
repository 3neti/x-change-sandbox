<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;

test('cockpit voucher detail and distribution workspace render for an existing pay code', function () {
    $code = DB::table('vouchers')
        ->whereNotNull('code')
        ->latest('id')
        ->value('code');

    if (! is_string($code) || $code === '') {
        $this->markTestSkipped('No local Pay Code exists for Cockpit Voucher Detail browser smoke.');
    }

    $user = User::query()->where('email', 'dusk-cockpit-voucher-detail@example.test')->first();

    if (! $user instanceof User) {
        $user = User::factory()->create([
            'name' => 'Dusk Cockpit Voucher Detail Operator',
            'email' => 'dusk-cockpit-voucher-detail@example.test',
            'mobile' => '63918'.random_int(10000000, 99999999),
            'password' => Hash::make('password'),
        ]);
    }

    $this->browse(function (Browser $browser) use ($code, $user): void {
        $browser
            ->loginAs($user)
            ->visit("/x/cockpit/pay-codes/{$code}")
            ->waitForText('Pay Code Detail', 10)
            ->assertPathIs("/x/cockpit/pay-codes/{$code}")
            ->assertPresent('[data-testid="cockpit-voucher-detail-primary-summary"]')
            ->assertPresent('[data-testid="cockpit-voucher-detail-connected-context"]')
            ->assertPresent('[data-testid="cockpit-voucher-integration-summary-panel"]')
            ->assertSee('Pay Code facts')
            ->assertSee('Audit, follow-up, and notification status')
            ->assertSee('Open distribution workspace')
            ->assertDontSee('provider_payload')
            ->assertDontSee('raw_payload')
            ->visit("/x/cockpit/pay-codes/{$code}/distribution")
            ->waitForText('Distribution Workspace', 10)
            ->assertPathIs("/x/cockpit/pay-codes/{$code}/distribution")
            ->assertPresent('[data-testid="cockpit-distribution-primary-summary"]')
            ->assertPresent('[data-testid="cockpit-distribution-connected-context-summary"]')
            ->assertPresent('[data-testid="cockpit-distribution-analytics-panel"]')
            ->assertDontSee('provider_payload')
            ->assertDontSee('raw_payload');
    });
});
