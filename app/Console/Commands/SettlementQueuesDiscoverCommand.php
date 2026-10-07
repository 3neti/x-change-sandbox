<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\QueueOperations\InstalledQueueManifestDiscovery;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('settlement-os:queues:discover {--json : Emit JSON}')]
#[Description('Discover versioned Settlement OS queue manifests from installed packages')]
final class SettlementQueuesDiscoverCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(InstalledQueueManifestDiscovery $discovery): int
    {
        $manifests = $discovery->discover();

        if ($this->option('json')) {
            $this->line(json_encode($manifests, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

            return self::SUCCESS;
        }

        $this->table(
            ['Package', 'Version', 'Status', 'Lanes'],
            array_map(static fn (array $manifest): array => [
                $manifest['package'],
                $manifest['version'] ?? 'local',
                $manifest['status'],
                implode(', ', array_keys($manifest['lanes'])) ?: 'none',
            ], $manifests),
        );

        return self::SUCCESS;
    }
}
