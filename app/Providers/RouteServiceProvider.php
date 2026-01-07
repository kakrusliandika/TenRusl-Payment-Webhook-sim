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
        // NOTE:
        // Semua limit di sini sengaja dibuat configurable via config('tenrusl.*')
        // supaya tuning production tidak perlu redeploy.
        //
        // IMPORTANT:
        // Jangan pernah membaca env() di sini. Saat config cache aktif, .env tidak di-load
        // dan env() di luar file config dapat mengembalikan null.

        $webPerMinute = (int) config('tenrusl.rate_limit.web.per_minute', 240);

        $apiPerSecond = (int) config('tenrusl.rate_limit.api.per_second', 5);
        $apiPerMinute = (int) config('tenrusl.rate_limit.api.per_minute', 120);

        $whDefaultPerSecond = (int) config('tenrusl.rate_limit.webhooks.per_second', 10);
        $whDefaultPerMinute = (int) config('tenrusl.rate_limit.webhooks.per_minute', 600);

        $whKeyBy = (string) config('tenrusl.rate_limit.webhooks.key_by', 'ip_provider');

        /** @var array<string, array{per_second?: int, per_minute?: int}> $whProviderOverrides */
        $whProviderOverrides = (array) config('tenrusl.rate_limit.webhooks.providers', []);

        // General web browsing
        RateLimiter::for('web', function (Request $request) use ($webPerMinute) {
            return [
                Limit::perMinute(max(1, $webPerMinute))->by('web:min:'.self::rateKey($request)),
            ];
        });

        // Public API access (non-webhook)
        RateLimiter::for('api', function (Request $request) use ($apiPerSecond, $apiPerMinute) {
            $key = self::rateKey($request);

            return [
                // Burst window
                Limit::perSecond(max(1, $apiPerSecond))->by('api:sec:'.$key),
                // Sustained window
                Limit::perMinute(max(1, $apiPerMinute))->by('api:min:'.$key),
            ];
        });

        // Webhooks: strict burst + sustained window
        RateLimiter::for('webhooks', function (Request $request) use ($whDefaultPerSecond, $whDefaultPerMinute, $whProviderOverrides, $whKeyBy) {
            $key = self::webhookRateKey($request, $whKeyBy);

            $provider = strtolower((string) $request->route('provider', ''));
            $provider = $provider !== '' ? $provider : 'global';

            $override = $whProviderOverrides[$provider] ?? null;
            $perSecond = is_array($override) && isset($override['per_second'])
                ? (int) $override['per_second']
                : $whDefaultPerSecond;

            $perMinute = is_array($override) && isset($override['per_minute'])
                ? (int) $override['per_minute']
                : $whDefaultPerMinute;

            return [
                // Burst: kalau kamu tes “burst 200 request”, ini harus cepat nahan.
                Limit::perSecond(max(1, $perSecond))->by('wh:sec:'.$key),
                // Sustained
                Limit::perMinute(max(1, $perMinute))->by('wh:min:'.$key),
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
     * - Strategi ditentukan oleh config('tenrusl.rate_limit.webhooks.key_by'):
     *   - ip: berdasarkan IP (butuh TrustProxies benar)
     *   - provider: berdasarkan provider path param saja
     *   - ip_provider: gabungan provider + ip
     * - Jika trusted proxies tidak diset, jangan bergantung pada IP (bisa salah: IP proxy),
     *   fallback ke provider saja.
     */
    protected static function webhookRateKey(Request $request, string $keyBy): string
    {
        $provider = strtolower((string) $request->route('provider', 'global'));
        $provider = $provider !== '' ? $provider : 'global';

        $keyBy = strtolower(trim($keyBy));
        if ($keyBy === '') {
            $keyBy = 'ip_provider';
        }

        $useIp = self::webhookUsesIp();
        $ip = $useIp ? $request->ip() : null;
        $ip = is_string($ip) ? trim($ip) : '';
        $hasIp = $ip !== '';

        // Jika disuruh pakai IP tapi kita tidak punya IP yang valid (proxy belum trusted),
        // fallback ke provider supaya tidak “open season”.
        return match ($keyBy) {
            'provider' => "provider:{$provider}",
            'ip' => $hasIp ? "ip:{$ip}" : "provider:{$provider}",
            'ip_provider', 'provider_ip' => $hasIp ? "provider:{$provider}|ip:{$ip}" : "provider:{$provider}",
            default => $hasIp ? "provider:{$provider}|ip:{$ip}" : "provider:{$provider}",
        };
    }

    /**
     * Anggap aman memakai IP untuk throttling webhook hanya jika TRUSTED_PROXIES diset.
     * (Di lingkungan proxy/LB, ini wajib supaya request()->ip() adalah IP klien asli.)
     */
    protected static function webhookUsesIp(): bool
    {
        $trusted = config('tenrusl.trusted_proxies');

        if (is_string($trusted)) {
            return trim($trusted) !== '';
        }

        if (is_array($trusted)) {
            return $trusted !== [];
        }

        return false;
    }
}
