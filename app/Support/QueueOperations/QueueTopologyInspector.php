<?php

declare(strict_types=1);

namespace App\Support\QueueOperations;

final readonly class QueueTopologyInspector
{
    public function __construct(private InstalledQueueManifestDiscovery $discovery) {}

    /**
     * @return array{
     *     ready: bool,
     *     horizon_enabled: bool,
     *     queue_connection: string,
     *     horizon_connection: string,
     *     retry_after: int|null,
     *     authorized_queues: list<string>,
     *     declared_queues: list<string>,
     *     manifests: list<array<string, mixed>>,
     *     errors: list<string>,
     *     warnings: list<string>
     * }
     */
    public function inspect(): array
    {
        $manifests = $this->discovery->discover();
        $enabled = (bool) config('queue-operations.horizon_enabled', false);
        $connection = (string) config('queue.default');
        $horizonConnection = (string) config('queue-operations.connection', 'redis');
        $retryAfter = config("queue.connections.{$horizonConnection}.retry_after");
        $retryAfter = is_numeric($retryAfter) ? (int) $retryAfter : null;
        $authorizedQueues = array_values(array_unique(config('queue-operations.authorized_queues', [])));
        $declaredQueues = [];
        $financialQueues = [];
        $queueSemantics = [];
        $errors = [];
        $warnings = [];

        foreach ($manifests as $manifest) {
            foreach ($manifest['lanes'] as $lane => $definition) {
                $queue = $definition['queue'] ?? null;

                if (! is_string($queue) || $queue === '') {
                    $errors[] = "{$manifest['package']}:{$lane} has no valid queue name.";

                    continue;
                }

                $declaredQueues[] = $queue;
                $semantics = [
                    'criticality' => $definition['criticality'] ?? null,
                    'ordering' => $definition['ordering'] ?? null,
                    'explicit_commissioning' => $definition['explicit_commissioning'] ?? null,
                ];

                if (isset($queueSemantics[$queue]) && $queueSemantics[$queue] !== $semantics) {
                    $errors[] = "Queue [{$queue}] is declared with incompatible semantics.";
                } else {
                    $queueSemantics[$queue] = $semantics;
                }

                if (($definition['criticality'] ?? null) === 'financial') {
                    $financialQueues[] = $queue;

                    if ($queue === 'default') {
                        $errors[] = "{$manifest['package']}:{$lane} is financial and may not use default.";
                    }
                }

                $timeout = data_get($definition, 'recommended.timeout');
                $maxProcesses = data_get($definition, 'recommended.max_processes');
                $processCeiling = (int) config('queue-operations.max_processes_ceiling', 4);

                if ($enabled && in_array($queue, $authorizedQueues, true)
                    && (! is_numeric($timeout) || $retryAfter === null || (int) $timeout >= $retryAfter)) {
                    $errors[] = "{$manifest['package']}:{$lane} timeout must be lower than "
                        ."[{$horizonConnection}] retry_after.";
                }

                if ($enabled && in_array($queue, $authorizedQueues, true)
                    && is_numeric($maxProcesses) && (int) $maxProcesses > $processCeiling) {
                    $errors[] = "{$manifest['package']}:{$lane} exceeds the approved process ceiling [{$processCeiling}].";
                }
            }
        }

        $declaredQueues = array_values(array_unique($declaredQueues));
        $financialQueues = array_values(array_unique($financialQueues));

        foreach (array_diff($authorizedQueues, $declaredQueues) as $queue) {
            $errors[] = "Authorized queue [{$queue}] is not declared by an installed package.";
        }

        foreach (array_diff($financialQueues, $authorizedQueues) as $queue) {
            $message = "Financial queue [{$queue}] is declared but not commissioned.";
            $warnings[] = $message;
        }

        if ($enabled && $horizonConnection !== 'redis') {
            $errors[] = 'Horizon supervisors require HORIZON_QUEUE_CONNECTION=redis.';
        }

        if ($enabled && $authorizedQueues === []) {
            $errors[] = 'Horizon is enabled without any explicitly authorized queues.';
        }

        if ($manifests === []) {
            $warnings[] = 'No installed package publishes a Settlement OS queue manifest yet.';
        }

        return [
            'ready' => $errors === [],
            'horizon_enabled' => $enabled,
            'queue_connection' => $connection,
            'horizon_connection' => $horizonConnection,
            'retry_after' => $retryAfter,
            'authorized_queues' => $authorizedQueues,
            'declared_queues' => $declaredQueues,
            'manifests' => $manifests,
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }
}
