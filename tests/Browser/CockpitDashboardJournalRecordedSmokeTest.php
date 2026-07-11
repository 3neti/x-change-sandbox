<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use LBHurtado\XChange\Models\CockpitOperatorIssuanceActivity;

test('cockpit dashboard renders a journal recorded operator activity fixture', function () {
    $activityId = 'dusk-cockpit-journal-recorded-activity';
    $code = 'PC-DUSK-JOURNAL';
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
            ->assertSee('journal: recorded')
            ->assertSee('Writes journal')
            ->assertSee('Writes journal: yes')
            ->assertSee('Journal entry')
            ->assertSee('journal-entry-local-fixture')
            ->assertSee('Diagnostic: Journal recorded')
            ->assertSee('Reference')
            ->assertSee('ERN-LOCAL-COCKPIT-0001');
    });
});
