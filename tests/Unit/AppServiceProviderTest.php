<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Application;

it('does not configure development API documentation in production', function (): void {
    $application = new Application(dirname(__DIR__, 2));
    $application->instance('env', 'production');

    $provider = new class($application) extends AppServiceProvider
    {
        public function configureApiDocumentationForTest(): void
        {
            $this->configureApiDocumentation();
        }
    };

    expect(fn () => $provider->configureApiDocumentationForTest())
        ->not->toThrow(Throwable::class);
});

it('keeps Scramble as a development-only dependency', function (): void {
    $composer = json_decode(
        file_get_contents(dirname(__DIR__, 2).'/composer.json'),
        true,
        flags: JSON_THROW_ON_ERROR,
    );

    expect($composer['require'])->not->toHaveKey('dedoc/scramble');
    expect($composer['require-dev'])->toHaveKey('dedoc/scramble');
});
