<?php

it('includes a session csrf token in the app layout', function (): void {
    $this->withoutVite();

    $this->get('/login')
        ->assertOk()
        ->assertSee('<meta name="csrf-token" content="'.csrf_token().'">', false);
});
