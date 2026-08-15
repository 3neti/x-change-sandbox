<?php

declare(strict_types=1);

use Composer\InstalledVersions;

it('consumes x-mcp as an external tagged package with deployment guidance', function (): void {
    $root = dirname(__DIR__, 2);
    $composer = json_decode(
        file_get_contents($root.'/composer.json'),
        true,
        flags: JSON_THROW_ON_ERROR,
    );
    $environmentExample = file_get_contents($root.'/.env.example');

    expect($root.'/packages/x-mcp')->not->toBeDirectory();
    expect($composer['require']['3neti/x-mcp'])
        ->toBe('v0.1.0')
        ->not->toContain('dev-');
    expect(InstalledVersions::getPrettyVersion('3neti/x-mcp'))->toBe('v0.1.0');
    expect($environmentExample)
        ->toContain('XMCP_ENABLED=false')
        ->toContain('XMCP_API_BASE_URL=https://example.test/api/partner/v1')
        ->toContain('XMCP_EXPECTED_PARTNER_CONTRACT_VERSION=1.0.0')
        ->toContain('XCHANGE_PARTNER_API_ENABLED=false');
});

it('publishes Passport migrations as host-owned deployment plumbing', function (): void {
    $migrations = glob(dirname(__DIR__, 2).'/database/migrations/*_create_oauth_*_table.php');

    expect($migrations)->toHaveCount(5);
});
