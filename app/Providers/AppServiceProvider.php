<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Dedoc\Scramble\Scramble;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
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
        $this->configureDefaults();
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
     * Configure development-only API documentation.
     */
    protected function configureApiDocumentation(): void
    {
        if (! $this->app->isLocal() || ! class_exists(Scramble::class)) {
            return;
        }

        Scramble::configure()
            ->routes(function (Route $route): bool {
                $action = $route->getActionName();

                if (! is_string($action)) {
                    return false;
                }

                return Str::startsWith($route->uri(), 'api/x/v1')
                    && Str::startsWith($action, 'LBHurtado\\XChange\\Lifecycle\\Http\\Controllers\\');
            });
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
