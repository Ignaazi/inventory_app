<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // 1. Import Facade URL

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
        // 2. Paksa HTTPS jika aplikasi diakses melalui HTTPS / Ngrok proxy
        if (request()->server->has('HTTP_X_FORWARDED_PROTO') && request()->server->get('HTTP_X_FORWARDED_PROTO') === 'https') {
            URL::forceScheme('https');
        }

        // Atau jika ingin memaksa HTTPS di environment selain 'local':
        /*
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }
        */
    }
}