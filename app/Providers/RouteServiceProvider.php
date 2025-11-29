<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureRateLimiting();

        // Routes HTTP (web/api) di-load via bootstrap/app.php (Laravel 11/12 style).
        // Di sini kita tidak perlu lagi memanggil Route::middleware()->group() manual.
    }

    /**
     * Konfigurasi rate limiting untuk web, api, dan webhooks.
     */
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
                // Fine-grained
                Limit::perSecond(5)->by(self::rateKey($request)),
                // Sustained window
                Limit::perMinute(120)->by(self::rateKey($request)),
            ];
        });

        // Webhooks: strict burst + sustained window
        RateLimiter::for('webhooks', function (Request $request) {
            // Untuk webhook, kunci by IP sudah cukup (gateway biasanya IP jelas).
            // Kalau mau lebih spesifik, bisa by(provider) atau kombinasi.
            return [
                Limit::perSecond(10)->by(self::rateKey($request)),
                Limit::perMinute(600)->by(self::rateKey($request)),
            ];
        });

        // RateLimiter::for('webhooks', function (Request $request) {
        //     $provider = (string) $request->route('provider', 'global');
        //     return [
        //         Limit::perSecond(10)->by('wh:' . $provider),
        //         Limit::perMinute(600)->by('wh:' . $provider),
        //     ];
        // });

    }

    /**
     * Key umum rate limiting: default pakai IP.
     */
    protected static function rateKey(Request $request): string
    {
        return $request->ip() ?: 'global';
    }
}
