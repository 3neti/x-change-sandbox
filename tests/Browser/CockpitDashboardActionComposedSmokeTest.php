<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use LBHurtado\XChange\Models\CockpitOperatorIssuanceActivity;

test('cockpit dashboard renders an action composed operator activity fixture', function () {
    $activityId = 'dusk-cockpit-action-composed-activity';
    $code = 'PC-DUSK-ACTION';
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
        '--activity-id' => $activityId,
        '--code' => $code,
        '--operator-id' => (string) $user->getKey(),
        '--json' => true,
    ]);

    expect(Artisan::output())->toContain($activityId);

    $this->browse(function (Browser $browser) use ($user, $code): void {
        $browser
            ->loginAs($user)
            ->visit('/x/cockpit')
            ->waitForText("Pay Code {$code} issued", 10)
            ->assertPathIs('/x/cockpit')
            ->assertSee("Pay Code {$code} issued")
            ->assertSee('action: composed')
            ->assertSee('Action hint')
            ->assertSee('Action hint: cockpit.pay-code.open')
            ->assertSee('Action run')
            ->assertSee('Action run: action-run-local-fixture')
            ->assertSee('Executes action: no')
            ->assertSee('Suggested action: Open Pay Code')
            ->assertSee('Reason: Synthetic local x-action fixture for Cockpit diagnostic visual verification.');
    });
});
