<?php

declare(strict_types=1);

namespace App\Support\QueueOperations;

use Composer\InstalledVersions;
use InvalidArgumentException;

final readonly class InstalledQueueManifestDiscovery
{
    /** @param array<string, string>|null $packageRoots */
    public function __construct(private ?array $packageRoots = null) {}

    /**
     * @return list<array{
     *     package: string,
     *     version: string|null,
     *     manifest_path: string,
     *     status: string,
     *     lanes: array<string, array<string, mixed>>
     * }>
     */
    public function discover(): array
    {
        $manifests = [];

        foreach ($this->packageRoots() as $package => $root) {
            $composerPath = $root.'/composer.json';

            if (! is_file($composerPath)) {
                continue;
            }

            $composer = json_decode(
                file_get_contents($composerPath),
                true,
                flags: JSON_THROW_ON_ERROR,
            );
            $relativePath = data_get($composer, 'extra.settlement-os.queue-manifest');

            if (! is_string($relativePath) || trim($relativePath) === '') {
                continue;
            }

            $manifestPath = realpath($root.'/'.$relativePath);
            $realRoot = realpath($root);

            if ($manifestPath === false || $realRoot === false
                || ! str_starts_with($manifestPath, $realRoot.DIRECTORY_SEPARATOR)) {
                throw new InvalidArgumentException("Queue manifest for [{$package}] is outside its package root or missing.");
            }

            $manifest = require $manifestPath;
            $this->validate($package, $manifest);
            $manifests[] = [
                'package' => $package,
                'version' => InstalledVersions::isInstalled($package)
                    ? InstalledVersions::getPrettyVersion($package)
                    : null,
                'manifest_path' => $manifestPath,
                'status' => (string) ($manifest['status'] ?? 'active'),
                'lanes' => $manifest['lanes'],
            ];
        }

        usort($manifests, static fn (array $left, array $right): int => $left['package'] <=> $right['package']);

        return $manifests;
    }

    /** @return array<string, string> */
    private function packageRoots(): array
    {
        if ($this->packageRoots !== null) {
            return $this->packageRoots;
        }

        $roots = [];

        foreach (InstalledVersions::getInstalledPackages() as $package) {
            $path = InstalledVersions::getInstallPath($package);

            if (is_string($path)) {
                $roots[$package] = $path;
            }
        }

        return $roots;
    }

    private function validate(string $package, mixed $manifest): void
    {
        if (! is_array($manifest)
            || ($manifest['schema'] ?? null) !== 'settlement-os.queue-topology.v1'
            || ($manifest['package'] ?? null) !== $package
            || ! is_array($manifest['lanes'] ?? null)) {
            throw new InvalidArgumentException("Queue manifest for [{$package}] is invalid.");
        }
    }
}
