<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

final class OySignature
{
    /**
     * OY! Authorization Callback (simulator-friendly).
     *
     * Skema verifikasi:
     *  - Bearer token: Authorization: "Bearer {OY_CALLBACK_SECRET}"
     *  - Static token: "X-Callback-Auth" atau "X-OY-Callback-Auth"
     *  - HMAC hex: "X-OY-Signature" = HMAC-SHA256(rawBody, OY_CALLBACK_SECRET)
     *
     * Opsional: whitelist IP via env OY_IP_WHITELIST="1.2.3.4,5.6.7.8".
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        $secret = config('tenrusl.oy_callback_secret');
        if (!is_string($secret) || $secret === '') {
            return false;
        }

        // Optional: IP whitelist (comma-separated)
        $ipWhitelist = config('tenrusl.oy_ip_whitelist', '');
        $ipWhitelist = is_string($ipWhitelist) ? trim($ipWhitelist) : '';

        if ($ipWhitelist !== '') {
            $allowed = array_filter(array_map('trim', explode(',', $ipWhitelist)));
            if (!empty($allowed) && !in_array((string) $request->ip(), $allowed, true)) {
                return false;
            }
        }

        // A) Authorization: Bearer <secret>
        $auth = self::headerString($request, 'Authorization');
        if ($auth !== null && preg_match('/^Bearer\s+(.+)$/i', $auth, $m) === 1) {
            if (hash_equals($secret, trim((string) $m[1]))) {
                return true;
            }
        }

        // B) Static token header
        $static = self::headerString($request, 'X-Callback-Auth')
            ?? self::headerString($request, 'X-OY-Callback-Auth');

        if ($static !== null && hash_equals($secret, trim($static))) {
            return true;
        }

        // C) HMAC-SHA256 over raw body
        $sig = self::headerString($request, 'X-OY-Signature');
        if ($sig !== null) {
            $expected = hash_hmac('sha256', $rawBody, $secret);
            if (hash_equals(strtolower($expected), strtolower($sig))) {
                return true;
            }
        }

        return false;
    }

    private static function headerString(Request $request, string $key): ?string
    {
        $v = $request->header($key);

        if (!is_string($v)) {
            return null;
        }

        $v = trim($v);

        return $v !== '' ? $v : null;
    }
}
