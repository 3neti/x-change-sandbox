<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\QueueOperations\HorizonSupervisorFactory;
use App\Support\QueueOperations\InstalledQueueManifestDiscovery;
use Illuminate\Support\ServiceProvider;

class QueueOperationsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(InstalledQueueManifestDiscovery::class);

        if (config('queue-operations.dashboard_enabled', false)) {
            $horizonPath = trim((string) config('horizon.path', 'horizon'), '/ ');
            $readOnlyPaths = (array) config('x-change.commissioning.read_only_paths', []);

            config()->set('x-change.commissioning.read_only_paths', array_values(array_unique([
                ...$readOnlyPaths,
                $horizonPath,
                $horizonPath.'/*',
            ])));

            $operatorAccessPaths = (array) config(
                'x-change.commissioning.operator_access_paths',
                [],
            );

            foreach ([
                ['path' => 'login', 'methods' => ['GET', 'HEAD', 'POST']],
                ['path' => 'user/confirm-password', 'methods' => ['GET', 'HEAD', 'POST']],
                ['path' => 'two-factor-challenge', 'methods' => ['GET', 'HEAD', 'POST']],
                ['path' => 'logout', 'methods' => ['POST']],
            ] as $operatorAccessPath) {
                if (! in_array($operatorAccessPath, $operatorAccessPaths, true)) {
                    $operatorAccessPaths[] = $operatorAccessPath;
                }
            }

            config()->set(
                'x-change.commissioning.operator_access_paths',
                $operatorAccessPaths,
            );
        }

        if (! config('queue-operations.horizon_enabled', false)) {
            config()->set('horizon.environments', []);

            return;
        }

        $supervisors = (new HorizonSupervisorFactory)->make(
            $this->app->make(InstalledQueueManifestDiscovery::class)->discover(),
            config('queue-operations.authorized_queues', []),
            (string) config('queue-operations.connection', 'redis'),
        );

        config()->set('horizon.environments', ['*' => $supervisors]);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
