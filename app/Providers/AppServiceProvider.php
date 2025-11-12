<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;

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
        // Ensure Carbon and Laravel Date facade use the application locale
        try {
            $locale = config('app.locale', 'es');
            Carbon::setLocale($locale);
            Date::setLocale($locale);
        } catch (\Throwable $e) {
            // Fail silently if locale cannot be set
        }
    }
}
