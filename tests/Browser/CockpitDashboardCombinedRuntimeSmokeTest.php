<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use LBHurtado\XChange\Models\CockpitOperatorIssuanceActivity;

test('cockpit dashboard renders combined journal action and feedback operator activity fixture', function () {
    $activityId = 'dusk-cockpit-combined-runtime-activity';
    $code = 'PC-DUSK-COMBINED';
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
        '--with-action' => true,
        '--with-feedback' => true,
        '--activity-id' => $activityId,
        '--code' => $code,
        '--operator-id' => (string) $user->getKey(),
        '--json' => true,
    ]);

    $activity = CockpitOperatorIssuanceActivity::query()
        ->where('activity_id', $activityId)
        ->sole();

    expect($activity->journal_handoff_status)->toBe('recorded')
        ->and($activity->action_handoff_status)->toBe('composed')
        ->and($activity->feedback_handoff_status)->toBe('planned');

    $this->browse(function (Browser $browser) use ($user, $code): void {
        $browser
            ->loginAs($user)
            ->visit('/x/cockpit')
            ->waitForText("Pay Code {$code} issued", 10)
            ->assertPathIs('/x/cockpit')
            ->assertSee("Pay Code {$code} issued")
            ->assertSee('journal: recorded')
            ->assertSee('action: composed')
            ->assertSee('feedback: planned')
            ->assertSee('Writes journal: yes')
            ->assertSee('Action hint: cockpit.pay-code.open')
            ->assertSee('Executes action: no')
            ->assertSee('Feedback intent: cockpit.operator_issuance_activity.fixture')
            ->assertSee('Delivery plan: plan-local-fixture')
            ->assertSee('Sends feedback: no')
            ->assertSee('Channel: in_app');
    });
});
