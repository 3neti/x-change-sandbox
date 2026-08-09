<?php

use Composer\InstalledVersions;
use Inertia\Testing\AssertableInertia as Assert;

test('uses the installed x-change release tag for release metadata', function () {
    $installedXChangeVersion = InstalledVersions::getPrettyVersion('3neti/x-change');

    expect(config('app.version'))->toBe($installedXChangeVersion)
        ->and(config('app.x_change_version'))->toBe($installedXChangeVersion);
});

test('returns the configured application identity to the landing page', function () {
    config([
        'app.name' => 'Payout',
        'app.version' => 'v2026.08.09',
        'app.x_change_version' => 'v1.0.0-beta.122',
    ]);

    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->where('name', 'Payout')
            ->where('version', 'v2026.08.09')
            ->where('xChangeVersion', 'v1.0.0-beta.122'));
});
