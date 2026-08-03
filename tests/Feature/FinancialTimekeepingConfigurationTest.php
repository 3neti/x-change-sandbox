<?php

declare(strict_types=1);

it('stores financial timestamps in UTC by default', function (): void {
    expect(config('app.timezone'))->toBe('UTC');
});
