<?php

use Symfony\Component\Process\Process;

it('reports the host release and commissioning result through x-deploy', function (): void {
    $process = new Process([PHP_BINARY, 'artisan', 'x-deploy:report', '--json', '--no-interaction'], dirname(__DIR__, 2), [
        'XDEPLOY_INSTANCE_ID' => 'x-deploy-integration-test',
        'XDEPLOY_ENVIRONMENT' => 'testing',
        'XDEPLOY_REGISTRY_ENABLED' => 'false',
    ]);
    $process->mustRun();
    $report = json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR);

    expect($report['manifest']['instance_id'])->toBe('x-deploy-integration-test');
    expect($report['manifest']['environment'])->toBe('testing');
    expect(array_column($report['manifest']['packages'], 'name'))->toContain('3neti/x-change', '3neti/x-deploy');
    expect($report['commissioning'])->toBe(['passed' => true, 'issues' => []]);
});

it('passes the installed host deployment gate', function (string $command): void {
    $process = new Process([PHP_BINARY, 'artisan', $command, '--json', '--no-interaction'], dirname(__DIR__, 2));
    $process->mustRun();

    expect(json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR))->toBe(['passed' => true, 'issues' => []]);
})->with(['x-deploy:preflight', 'x-deploy:commission']);

it('emits a local heartbeat successfully with registry submission disabled', function (): void {
    $process = new Process([PHP_BINARY, 'artisan', 'x-deploy:heartbeat', '--json', '--no-interaction'], dirname(__DIR__, 2), [
        'XDEPLOY_INSTANCE_ID' => 'x-deploy-integration-test',
        'XDEPLOY_REGISTRY_ENABLED' => 'false',
    ]);
    $process->mustRun();
    $heartbeat = json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR);

    expect($heartbeat['reported'])->toBeFalse();
    expect($heartbeat['event']['instance_id'])->toBe('x-deploy-integration-test');
    expect($heartbeat['event']['type'])->toBe('heartbeat');
    expect(array_keys($heartbeat['event']['payload']))->toBe(['environment', 'git_commit', 'composer_lock_hash']);
});
