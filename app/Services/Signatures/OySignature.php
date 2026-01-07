<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

final class OySignature
{
    /**
     * Backward-compatible: return bool only.
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        return self::verifyWithReason($rawBody, $request)['ok'];
    }

    /**
     * OY! Callback (simulator-friendly).
     *
     * Accepted schemes (any one can pass):
     *  A) Authorization: Bearer {OY_CALLBACK_SECRET}
     *  B) X-Callback-Auth or X-OY-Callback-Auth equals {OY_CALLBACK_SECRET}
     *  C) X-OY-Signature = hex(HMAC-SHA256(rawBody, OY_CALLBACK_SECRET))
     *
     * Optional IP whitelist: OY_IP_WHITELIST="1.2.3.4,5.6.7.8"
     *
     * @return array{ok: bool, reason: string}
     */
    public static function verifyWithReason(string $rawBody, Request $request): array
    {
        $secret = config('tenrusl.oy_callback_secret');
        if (! is_string($secret) || trim($secret) === '') {
            return self::result(false, 'missing_secret');
        }

        // Optional: IP whitelist (comma-separated)
        $ipWhitelist = config('tenrusl.oy_ip_whitelist', '');
        $ipWhitelist = is_string($ipWhitelist) ? trim($ipWhitelist) : '';

        if ($ipWhitelist !== '') {
            $allowed = array_values(array_filter(array_map('trim', explode(',', $ipWhitelist))));
            if ($allowed !== [] && ! in_array((string) $request->ip(), $allowed, true)) {
                return self::result(false, 'ip_not_allowed');
            }
        }

        // A) Authorization: Bearer <secret>
        $auth = self::headerString($request, 'Authorization');
        if ($auth !== null && preg_match('/^Bearer\s+(.+)$/i', $auth, $m) === 1) {
            if (hash_equals($secret, trim((string) $m[1]))) {
                return self::result(true, 'ok_bearer');
            }
        }

        // B) Static token header
        $static = self::headerString($request, 'X-Callback-Auth')
            ?? self::headerString($request, 'X-OY-Callback-Auth');

        if ($static !== null) {
            if (hash_equals($secret, trim($static))) {
                return self::result(true, 'ok_static_token');
            }
        }

        // C) HMAC-SHA256 over raw body
        $sig = self::headerString($request, 'X-OY-Signature');
        if ($sig !== null) {
            $sigNorm = strtolower(trim($sig));
            if (str_starts_with($sigNorm, 'sha256=')) {
                $sigNorm = substr($sigNorm, 7);
            }

            $expected = hash_hmac('sha256', $rawBody, $secret); // hex lowercase
            if (hash_equals(strtolower($expected), $sigNorm)) {
                return self::result(true, 'ok_hmac');
            }

            return self::result(false, 'invalid_signature');
        }

        return self::result(false, 'missing_signature');
    }

    private static function headerString(Request $request, string $key): ?string
    {
        $v = $request->headers->get($key);
        if (! is_string($v)) {
            return null;
        }

        $v = trim($v);

        return $v !== '' ? $v : null;
    }

    /**
     * @return array{ok: bool, reason: string}
     */
    private static function result(bool $ok, string $reason): array
    {
        return ['ok' => $ok, 'reason' => $reason];
    }
}
