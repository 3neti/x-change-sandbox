<?php

use Composer\InstalledVersions;
use Inertia\Testing\AssertableInertia as Assert;

test('returns the configured application identity and x-change version to the landing page', function () {
    config([
        'app.name' => 'Payout',
    ]);

    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->where('name', 'Payout')
            ->where('xchange.version', InstalledVersions::getPrettyVersion('3neti/x-change')));
});
