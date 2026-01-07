<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * TrustProxies
 * ------------
 * Membuat Laravel “percaya” header X-Forwarded-* hanya dari proxy yang kamu whitelist,
 * supaya request()->ip() dan scheme (https) akurat di balik load balancer / reverse proxy.
 *
 * Env (diserap ke config saat deploy / config cache):
 * - TRUSTED_PROXIES="10.0.0.10,10.0.0.11" (comma-separated, boleh CIDR)
 * - TRUSTED_PROXIES="*" (trust all proxies; praktis tapi paling longgar)
 *
 * Catatan:
 * - Header proxy bisa dipalsukan; karena itu whitelist proxy itu penting.
 */
final class TrustProxies extends Middleware
{
    /**
     * Daftar proxy yang dipercaya.
     *
     * Laravel beberapa versi memakai property $proxies, sebagian versi memanggil method proxies().
     * Kita support dua-duanya supaya blueprint production tidak “diam-diam” salah IP.
     *
     * @var array<int, string>|string|null
     */
    protected array|string|null $proxies = null;

    /**
     * Header yang dipercaya dari proxy.
     *
     * Kita fokus pada yang dibutuhkan untuk:
     * - IP klien asli: X-Forwarded-For
     * - HTTPS detection: X-Forwarded-Proto (+ port)
     * - AWS ELB compatibility
     */
    protected int $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;

    /**
     * Tentukan daftar proxy yang dipercaya dari env TRUSTED_PROXIES (via config).
     *
     * @return array<int, string>|string|null
     */
    protected function proxies(): array|string|null
    {
        // Jika property $proxies belum diisi (mis. lewat constructor/bootstrapping), hitung di sini.
        if ($this->proxies === null) {
            $this->proxies = $this->resolveProxiesFromEnv();
        }

        return $this->proxies;
    }

    /**
     * Resolve proxies dari konfigurasi.
     *
     * IMPORTANT:
     * Jangan membaca env() di runtime middleware (config cache bisa aktif).
     * Nilai TRUSTED_PROXIES harus masuk lewat config('tenrusl.trusted_proxies').
     *
     * - production: jika kosong, log warning sekali (karena IP/scheme bisa salah).
     * - non-production: boleh kosong untuk kemudahan lokal.
     *
     * @return array<int, string>|string|null
     */
    private function resolveProxiesFromEnv(): array|string|null
    {
        /** @var mixed $value */
        $value = config('tenrusl.trusted_proxies');

        // Jika config sudah memberi array, pakai langsung.
        if (is_array($value)) {
            if ($value === []) {
                $this->warnIfProductionMissing();

                return null;
            }

            return $value;
        }

        // Jika config memberi string (mis. "*" atau csv), normalize di sini.
        if (! is_string($value)) {
            $this->warnIfProductionMissing();

            return null;
        }

        $value = trim($value);

        if ($value === '') {
            $this->warnIfProductionMissing();

            return null;
        }

        // Trust all proxies (paling longgar)
        if ($value === '*') {
            return '*';
        }

        $parts = array_values(array_filter(array_map('trim', explode(',', $value)), static fn ($p) => $p !== ''));

        return $parts !== [] ? $parts : null;
    }

    private function warnIfProductionMissing(): void
    {
        static $warned = false;

        if ($warned) {
            return;
        }

        $appEnv = strtolower((string) config('app.env', 'production'));
        if ($appEnv !== 'production') {
            return;
        }

        $warned = true;

        Log::warning('TRUSTED_PROXIES is not set in production. Client IP and scheme may be incorrect behind a proxy/LB.', [
            'app_env' => $appEnv,
        ]);
    }
}
