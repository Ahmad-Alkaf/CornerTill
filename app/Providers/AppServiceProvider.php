<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Paddle\Cashier;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Free beta: do not register the Paddle webhook route (/paddle/webhook).
        if (!config('features.payments_enabled'))
            Cashier::ignoreRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $url = (string) config('app.url');
        if (str_starts_with($url,'https'))
            URL::forceScheme('https');
    }
}