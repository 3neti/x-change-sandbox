<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use LBHurtado\XChange\Models\CockpitOperatorIssuanceActivity;

test('cockpit operator activity can navigate to pay code explorer context read only', function () {
    $activityId = 'dusk-cockpit-activity-explorer-bridge';
    $code = 'PC-DUSK-EXPLORER';
    $user = User::query()->where('email', 'dusk-cockpit-explorer@example.test')->first();

    if (! $user instanceof User) {
        $user = User::factory()->create([
            'name' => 'Dusk Cockpit Explorer Operator',
            'email' => 'dusk-cockpit-explorer@example.test',
            'mobile' => '639170000002',
            'password' => Hash::make('password'),
        ]);
    }

    CockpitOperatorIssuanceActivity::query()
        ->where('activity_id', $activityId)
        ->delete();

    Artisan::call('x-change:cockpit:seed-diagnostic-activity', [
        '--local-only' => true,
        '--activity-id' => $activityId,
        '--code' => $code,
        '--operator-id' => (string) $user->getKey(),
        '--json' => true,
    ]);

    expect(Artisan::output())->toContain($activityId);

    $this->browse(function (Browser $browser) use ($user, $code): void {
        $browser
            ->loginAs($user)
            ->visit("/x/cockpit?activity_search={$code}&activity_status=issued&activity_handoff_status=recorded")
            ->waitForText("Pay Code {$code} issued", 10)
            ->assertSee('Open in Explorer');

        $explorerHref = $browser->attribute('[data-testid="cockpit-operator-issuance-activity-explorer-link"]', 'href');

        expect($explorerHref)
            ->toContain('/x/cockpit/pay-codes')
            ->toContain("activity_code={$code}")
            ->toContain('activity_source=operator_issuance_activity');

        $browser
            ->visit($explorerHref)
            ->waitFor('[data-testid="cockpit-activity-navigation-context"]', 10)
            ->assertPathIs('/x/cockpit/pay-codes')
            ->assertQueryStringHas('activity_code', $code)
            ->assertQueryStringHas('activity_source', 'operator_issuance_activity')
            ->assertSee('ACTIVITY NAVIGATION CONTEXT')
            ->assertSee($code)
            ->assertSee('operator_issuance_activity')
            ->assertSee('activity-navigation-read-only')
            ->assertSee('activity-navigation-context-only')
            ->assertSee('Mutation blocked')
            ->assertDontSee('Enable handoffs')
            ->assertDontSee('Save configuration')
            ->assertDontSee('provider_payload')
            ->assertDontSee('raw_payload');
    });
});
