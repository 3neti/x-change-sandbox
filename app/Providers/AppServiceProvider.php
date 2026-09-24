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
        }
        $this->configureApiDocumentation();
        $this->configureDefaults();
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
