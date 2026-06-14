<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;
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
        Password::defaults(fn () => Password::min(12)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols());

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
