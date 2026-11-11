<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Dedoc\Scramble\Scramble;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        config()->set('filesystems.disks.envelope-private', array_replace(
            (array) config('filesystems.disks.s3', []),
            ['visibility' => 'private', 'throw' => true, 'report' => true],
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('campaign-policy.enabled', false)) {
            config()->set('services.pipedream.policy_completion_token', config('campaign-policy.token'));
            $transports = (array) config('x-change.settlement.policy_completion.transports', []);
            $transports['aui.personal-accident.provisional-cover@1.0.0'] = config('campaign-policy.disposition');
            config()->set('x-change.settlement.policy_completion.transports', $transports);
            $this->configureAuiDemoConnection();
        }
        $this->configureApiDocumentation();
        $this->configureStoredValueCollectors();
        $this->configureDefaults();
    }

    protected function configureStoredValueCollectors(): void
    {
        $operatorIds = config('stored_value_collection.operator_user_ids', []);

        if (! is_array($operatorIds) || $operatorIds === []) {
            return;
        }

        Gate::define('x-change.stored-value.collect', static fn ($user): bool => in_array(
            (int) $user->getAuthIdentifier(),
            $operatorIds,
            true,
        ));
    }

    protected function configureAuiDemoConnection(): void
    {
        $connections = config('settlement-envelope.connections', []);

        if (! is_array($connections) || array_key_exists('aui-demo', $connections)) {
            return;
        }

        $connections['aui-demo'] = [
            'driver' => 'http',
            'base_url' => config('campaign-policy.disposition.submission_endpoint'),
            'auth' => [
                'type' => 'bearer',
                'token' => config('campaign-policy.token'),
            ],
            'connect_timeout' => config('campaign-policy.disposition.connect_timeout_seconds'),
            'timeout' => config('campaign-policy.disposition.response_timeout_seconds'),
        ];

        config()->set('settlement-envelope.connections', $connections);
    }

    /**
     * Disable the obsolete development-only API documentation routes.
     */
    protected function configureApiDocumentation(): void
    {
        if (! class_exists(Scramble::class)) {
            return;
        }

        Scramble::configure()->expose(false);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
