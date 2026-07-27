<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
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
                URL::forceScheme('https');
            }
        }

        // Define rate limiter for order creation
        RateLimiter::for('orders', function (Request $request) {
            return $request->user()
                ? Limit::none()
                : Limit::perMinute(5)->by($request->ip());
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
