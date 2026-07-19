<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use LBHurtado\XChange\Models\CockpitOperatorIssuanceActivity;

test('cockpit dashboard renders read-only operator activity search filters', function () {
    $activityId = 'dusk-cockpit-activity-filter';
    $code = 'PC-DUSK-FILTER';
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
            ->visit("/x/cockpit?activity_search={$code}&activity_status=issued&activity_handoff_status=recorded")
            ->waitForText("Pay Code {$code} issued", 10)
            ->assertPathIs('/x/cockpit')
            ->assertQueryStringHas('activity_search', $code)
            ->assertQueryStringHas('activity_status', 'issued')
            ->assertQueryStringHas('activity_handoff_status', 'recorded')
            ->assertSee('SEARCH ACTIVITY')
            ->assertInputValue('activity_search', $code)
            ->assertSelected('activity_status', 'issued')
            ->assertSelected('activity_handoff_status', 'recorded')
            ->assertSee('3 active filters')
            ->assertSee('Read-only filter query; no activity mutation is executed.')
            ->assertSee("Search: {$code}")
            ->assertSee('Status: issued')
            ->assertSee('Follow-up: Recorded')
            ->assertSee('Clear search')
            ->assertSee('Clear status')
            ->assertSee('Clear follow-up')
            ->assertSee("Pay Code {$code} issued")
            ->assertSee('Journal: Recorded')
            ->assertDontSee('Enable handoffs')
            ->assertDontSee('Save configuration')
            ->assertDontSee('provider_payload')
            ->assertDontSee('raw_payload');
    });
});
