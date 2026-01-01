<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

/**
 * TrustProxies
 * ------------
 * Membuat Laravel “percaya” header X-Forwarded-* hanya dari proxy yang kamu whitelist,
 * supaya request()->ip() dan scheme (https) akurat di balik load balancer / reverse proxy.
 *
 * Env:
 * - TRUSTED_PROXIES="10.0.0.10,10.0.0.11" (comma-separated, boleh CIDR)
 * - TRUSTED_PROXIES="*" (trust all proxies; praktis tapi paling longgar)
 *
 * Catatan:
 * - Header proxy bisa dipalsukan; karena itu whitelist proxy itu penting.
 */
final class TrustProxies extends Middleware
{
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
     * Tentukan daftar proxy yang dipercaya dari env TRUSTED_PROXIES.
     *
     * @return array<int, string>|string|null
     */
    protected function proxies(): array|string|null
    {
        $value = env('TRUSTED_PROXIES');

        if (!is_string($value)) {
            return null;
        }

        $value = trim($value);

        if ($value === '') {
            return null;
        }

        // Trust all proxies (paling longgar)
        if ($value === '*') {
            return '*';
        }

        $parts = array_values(array_filter(array_map('trim', explode(',', $value)), static fn ($p) => $p !== ''));

        return $parts !== [] ? $parts : null;
    }
}
