<?php

declare(strict_types=1);

namespace App\Support\QueueOperations;

final readonly class HorizonSupervisorFactory
{
    /**
     * @param  list<array{package: string, lanes: array<string, array<string, mixed>>}>  $manifests
     * @param  list<string>  $authorizedQueues
     * @return array<string, array<string, mixed>>
     */
    public function make(array $manifests, array $authorizedQueues, string $connection): array
    {
        $supervisors = [];

        foreach ($manifests as $manifest) {
            foreach ($manifest['lanes'] as $lane => $definition) {
                $queue = $definition['queue'] ?? null;

                if (! is_string($queue) || ! in_array($queue, $authorizedQueues, true)) {
                    continue;
                }

                $recommended = $definition['recommended'] ?? [];
                $name = 'settlement-os-'.str_replace(['/', '_'], '-', $manifest['package'].'-'.$lane);
                $supervisors[$name] = [
                    'connection' => $connection,
                    'queue' => [$queue],
                    'balance' => 'simple',
                    'processes' => max(1, (int) ($recommended['max_processes'] ?? 1)),
                    'maxProcesses' => max(1, (int) ($recommended['max_processes'] ?? 1)),
                    'maxTime' => 0,
                    'maxJobs' => 0,
                    'memory' => 128,
                    'tries' => max(1, (int) ($recommended['tries'] ?? 1)),
                    'timeout' => max(1, (int) ($recommended['timeout'] ?? 60)),
                    'nice' => 0,
                ];
            }
        }

        return $supervisors;
    }
}
