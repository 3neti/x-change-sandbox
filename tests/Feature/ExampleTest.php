<?php

use Inertia\Testing\AssertableInertia as Assert;

test('returns the configured application name to the landing page', function () {
    config(['app.name' => 'Payout']);

    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->where('name', 'Payout'));
});
