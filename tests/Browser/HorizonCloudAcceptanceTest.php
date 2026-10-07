<?php

declare(strict_types=1);

use Laravel\Dusk\Browser;

test('an authenticated operator can inspect the read-only Cloud Horizon dashboard', function (): void {
    $baseUrl = rtrim((string) getenv('HORIZON_ACCEPTANCE_URL'), '/');
    $mobile = (string) getenv('HORIZON_ACCEPTANCE_MOBILE');
    $password = (string) getenv('HORIZON_ACCEPTANCE_PASSWORD');

    expect($baseUrl)->not->toBe('')
        ->and($mobile)->not->toBe('')
        ->and($password)->not->toBe('');

    $this->browse(function (Browser $browser) use ($baseUrl, $mobile, $password): void {
        $browser
            ->visit($baseUrl.'/horizon')
            ->waitForLocation('/login', 10)
            ->type('mobile', $mobile)
            ->type('password', $password)
            ->click('[data-test="login-button"]')
            ->waitForLocation('/user/confirm-password', 10)
            ->type('password', $password)
            ->click('[data-test="confirm-password-button"]')
            ->pause(1000)
            ->visit($baseUrl.'/horizon')
            ->waitForText('Inactive', 10)
            ->assertPathBeginsWith('/horizon')
            ->assertTitleContains('Horizon - Dashboard')
            ->assertSee('Inactive');
    });
})->skip(
    ! getenv('HORIZON_ACCEPTANCE_URL')
    || ! getenv('HORIZON_ACCEPTANCE_MOBILE')
    || ! getenv('HORIZON_ACCEPTANCE_PASSWORD'),
    'Cloud Horizon acceptance credentials were not provided.',
);
