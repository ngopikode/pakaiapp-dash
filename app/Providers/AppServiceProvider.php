<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
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
        $this->configureDefaults();

        if (!app()->runningInConsole()) {

            if (app()->environment('production')) {
                \Illuminate\Support\Facades\URL::forceScheme('https');
            }
        }

        // Define rate limiter for order creation
        \Illuminate\Support\Facades\RateLimiter::for('orders', function (\Illuminate\Http\Request $request) {
            return $request->user()
                ? \Illuminate\Cache\RateLimiting\Limit::none()
                : \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->ip());
        });

        // Auto-run migrations once safely
        if (!\Illuminate\Support\Facades\Cache::has('migrations_run_2026_05_26')) {
            try {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                \Illuminate\Support\Facades\Cache::forever('migrations_run_2026_05_26', true);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Migration auto-run failed: ' . $e->getMessage());
            }
        }

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            \App\Listeners\EnforceSessionLimits::class,
        );
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

        Password::defaults(fn(): ?Password => app()->isProduction()
            ? Password::min(8)
            //                ->mixedCase()
            //                ->letters()
            //                ->numbers()
            //                ->symbols()
            //                ->uncompromised()
            : null,
        );
    }
}
