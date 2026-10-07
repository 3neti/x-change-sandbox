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
