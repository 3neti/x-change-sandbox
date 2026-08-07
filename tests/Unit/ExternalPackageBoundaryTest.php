<?php

declare(strict_types=1);

use Composer\InstalledVersions;

it('consumes x-change as an external tagged package', function (): void {
    $root = dirname(__DIR__, 2);
    $composer = json_decode(
        file_get_contents($root.'/composer.json'),
        true,
        flags: JSON_THROW_ON_ERROR,
    );

    expect($root.'/packages/x-change')->not->toBeDirectory();
    expect($root.'/config/x-change.php')->not->toBeFile();
    expect($root.'/config/lifecycle-scenarios.php')->not->toBeFile();
    expect($root.'/config/onboarding.php')->not->toBeFile();
    expect($root.'/config/x-feedback.php')->not->toBeFile();
    expect($composer['require']['3neti/x-change'])
        ->not->toContain('dev-');

    foreach ($composer['repositories'] ?? [] as $repository) {
        expect($repository['type'] ?? null)->toBe('git');
        expect($repository['url'] ?? '')
            ->not->toContain('/Users/')
            ->not->toContain('packages/x-change');
    }

    expect(InstalledVersions::getPrettyVersion('3neti/x-change'))
        ->toBe('v1.0.0-beta.87');
});
