<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use LBHurtado\XChange\Models\CockpitOperatorIssuanceActivity;

test('cockpit dashboard renders a feedback planned operator activity fixture', function () {
    $activityId = 'dusk-cockpit-feedback-planned-activity';
    $code = 'PC-DUSK-FEEDBACK';
    $user = User::query()->where('email', 'dusk-cockpit@example.test')->first();

    if (! $user instanceof User) {
        $user = User::factory()->create([
            'name' => 'Dusk Cockpit Operator',
            'email' => 'dusk-cockpit@example.test',
            'mobile' => '639170000001',
            'password' => Hash::make('password'),
        ]);
    }

    CockpitOperatorIssuanceActivity::query()
        ->where('activity_id', $activityId)
        ->delete();

    Artisan::call('x-change:cockpit:seed-diagnostic-activity', [
        '--local-only' => true,
        '--with-feedback' => true,
        '--activity-id' => $activityId,
        '--code' => $code,
        '--operator-id' => (string) $user->getKey(),
        '--json' => true,
    ]);

    expect(CockpitOperatorIssuanceActivity::query()->where('activity_id', $activityId)->value('feedback_handoff_status'))
        ->toBe('planned');

    $this->browse(function (Browser $browser) use ($user, $code): void {
        $browser
            ->loginAs($user)
            ->visit('/x/cockpit')
            ->waitForText("Pay Code {$code} issued", 10)
            ->assertPathIs('/x/cockpit')
            ->assertSee("Pay Code {$code} issued")
            ->assertSee('feedback: planned')
            ->assertSee('Feedback intent')
            ->assertSee('Feedback intent: cockpit.operator_issuance_activity.fixture')
            ->assertSee('Delivery plan')
            ->assertSee('Delivery plan: plan-local-fixture')
            ->assertSee('Sends feedback: no')
            ->assertSee('Channel: in_app')
            ->assertSee('Planned deliveries: 1')
            ->assertSee('Reason: Synthetic local x-feedback fixture for Cockpit diagnostic visual verification.');
    });
});
