<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureRateLimiting();
        // Routes are loaded via bootstrap/app.php
    }

    protected function configureRateLimiting(): void
    {
        // General web browsing
        RateLimiter::for('web', function (Request $request) {
            return [
                Limit::perMinute(240)->by(self::rateKey($request)),
            ];
        });

        // Public API access
        RateLimiter::for('api', function (Request $request) {
            return [
                Limit::perSecond(5)->by(self::rateKey($request)),   // fine-grained
                Limit::perMinute(120)->by(self::rateKey($request)), // sustained
            ];
        });

        // Webhooks: strict burst + sustained window
        RateLimiter::for('webhooks', function (Request $request) {
            return [
                Limit::perSecond(10)->by(self::rateKey($request)),
                Limit::perMinute(600)->by(self::rateKey($request)),
            ];
        });
    }

    protected static function rateKey(Request $request): string
    {
        return $request->ip() ?: 'global';
    }
}
