<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

final class XenditSignature
{
    /**
     * Backward-compatible: return bool only.
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        return self::verifyWithReason($rawBody, $request)['ok'];
    }

    /**
     * Standardized output:
     * - ok: true|false
     * - reason: short code for audit/logging (no secrets)
     *
     * @return array{ok: bool, reason: string}
     */
    public static function verifyWithReason(string $rawBody, Request $request): array
    {
        $expected = config('tenrusl.xendit_callback_token');
        if (!is_string($expected) || trim($expected) === '') {
            return self::result(false, 'missing_expected_token');
        }

        $token = self::headerString($request, 'x-callback-token')
            ?? self::headerString($request, 'X-CALLBACK-TOKEN')
            ?? self::headerString($request, 'X-Callback-Token');

        if ($token === null) {
            return self::result(false, 'missing_token_header');
        }

        if (hash_equals(trim($expected), $token)) {
            return self::result(true, 'ok');
        }

        return self::result(false, 'invalid_token');
    }

    private static function headerString(Request $request, string $key): ?string
    {
        $v = $request->headers->get($key);
        if (!is_string($v)) {
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
