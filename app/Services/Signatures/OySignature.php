<?php

namespace App\Services\Signatures;

use Illuminate\Http\Request;

class OySignature
{
    /**
     * OY! Authorization Callback (simulator-friendly).
     *
     * OY! menyediakan mekanisme "Authorization Callback" berbasis OAuth 2.0 agar
     * callback melewati proses otorisasi lebih dulu. Dalam simulator ini kita sediakan
     * 3 skema verifikasi ringan yang umum dipakai merchant:
     *  - Bearer token di header Authorization: "Bearer {OY_CALLBACK_SECRET}"
     *  - Static token di header "X-Callback-Auth" (atau "X-OY-Callback-Auth")
     *  - HMAC hex di header "X-OY-Signature" = HMAC-SHA256(rawBody, OY_CALLBACK_SECRET)
     *
     * Opsional: whitelist IP via env OY_IP_WHITELIST="1.2.3.4,5.6.7.8".
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        $secret = (string) config('tenrusl.oy_callback_secret');
        if ($secret === '' || $secret === null) {
            return false;
        }

        // Optional: IP whitelist (comma-separated)
        $ipWhitelist = trim((string) config('tenrusl.oy_ip_whitelist', ''));
        if ($ipWhitelist !== '') {
            $allowed = array_filter(array_map('trim', explode(',', $ipWhitelist)));
            if (!empty($allowed) && !in_array($request->ip(), $allowed, true)) {
                return false;
            }
        }

        // A) Authorization: Bearer <secret>
        $auth = $request->header('Authorization');
        if ($auth && preg_match('/^Bearer\s+(.+)$/i', $auth, $m)) {
            if (hash_equals($secret, trim($m[1]))) {
                return true;
            }
        }

        // B) Static token header
        $static = $request->header('X-Callback-Auth') ?? $request->header('X-OY-Callback-Auth');
        if (is_string($static) && $static !== '') {
            if (hash_equals($secret, trim($static))) {
                return true;
            }
        }

        // C) HMAC-SHA256 over raw body
        $sig = $request->header('X-OY-Signature');
        if (is_string($sig) && $sig !== '') {
            $expected = hash_hmac('sha256', $rawBody, $secret);
            if (hash_equals(strtolower($expected), strtolower($sig))) {
                return true;
            }
        }

        return false;
    }
}
