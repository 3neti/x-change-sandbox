<?php

declare(strict_types=1);

use Inertia\Testing\AssertableInertia as Assert;

it('makes developer access available to guests and redirects the legacy guide', function (): void {
    $this->withoutVite();

    $this->get('/docs/api')->assertRedirect('/x/developers');
    $this->get('/x/developers')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('x-change/public/DeveloperAccess')->missing('client_secret')->etc());
});
