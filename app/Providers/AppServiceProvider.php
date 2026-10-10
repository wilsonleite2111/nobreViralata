<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
        // Public forms: a burst limit against scripts and a daily cap against slow, steady abuse.
        RateLimiter::for('public-forms', fn (Request $request): array => [
            Limit::perMinute(5)->by('minute:'.$request->ip()),
            Limit::perDay(30)->by('day:'.$request->ip()),
        ]);
    }
}
