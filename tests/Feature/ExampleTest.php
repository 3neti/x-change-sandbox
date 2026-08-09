<?php

use Composer\InstalledVersions;
use Inertia\Testing\AssertableInertia as Assert;

test('uses the installed x-change release tag as the application version', function () {
    expect(config('app.version'))->toBe(
        InstalledVersions::getPrettyVersion('3neti/x-change'),
    );
});

test('returns the configured application identity to the landing page', function () {
    config([
        'app.name' => 'Payout',
        'app.version' => 'v2026.08.09',
    ]);

    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->where('name', 'Payout')
            ->where('version', 'v2026.08.09'));
});
