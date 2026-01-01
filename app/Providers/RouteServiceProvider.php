<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

final class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    /**
     * Konfigurasi rate limiting untuk web, api, dan webhooks.
     */
    protected function configureRateLimiting(): void
    {
        // General web browsing
        RateLimiter::for('web', function (Request $request) {
            return [
                Limit::perMinute(240)->by('web:min:' . self::rateKey($request)),
            ];
        });

        // Public API access (non-webhook)
        RateLimiter::for('api', function (Request $request) {
            $key = self::rateKey($request);

            return [
                // Burst window
                Limit::perSecond(5)->by('api:sec:' . $key),
                // Sustained window
                Limit::perMinute(120)->by('api:min:' . $key),
            ];
        });

        // Webhooks: strict burst + sustained window
        RateLimiter::for('webhooks', function (Request $request) {
            $key = self::webhookRateKey($request);

            return [
                // Burst: kalau kamu tes “burst 200 request”, ini harus cepat nahan.
                Limit::perSecond(10)->by('wh:sec:' . $key),
                // Sustained
                Limit::perMinute(600)->by('wh:min:' . $key),
            ];
        });
    }

    /**
     * Key umum rate limiting: pakai IP (setelah TrustProxies benar).
     */
    protected static function rateKey(Request $request): string
    {
        return $request->ip() ?: 'global';
    }

    /**
     * Key webhook:
     * - Jika TRUSTED_PROXIES dikonfigurasi (TrustProxies aktif), segmentasi dengan IP + provider.
     * - Jika tidak, jangan bergantung pada IP (bisa salah: IP proxy), fallback ke provider saja.
     */
    protected static function webhookRateKey(Request $request): string
    {
        $provider = strtolower((string) $request->route('provider', 'global'));
        $provider = $provider !== '' ? $provider : 'global';

        if (self::webhookUsesIp()) {
            $ip = $request->ip();
            if ($ip !== null && $ip !== '') {
                return "provider:{$provider}|ip:{$ip}";
            }
        }

        return "provider:{$provider}";
    }

    /**
     * Anggap aman memakai IP untuk throttling webhook hanya jika TRUSTED_PROXIES diset.
     * (Di lingkungan proxy/LB, ini wajib supaya request()->ip() adalah IP klien asli.)
     */
    protected static function webhookUsesIp(): bool
    {
        $trusted = env('TRUSTED_PROXIES');

        return is_string($trusted) && trim($trusted) !== '';
    }
}
