<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

final class LemonSqueezySignature
{
    /**
     * Backward-compatible: return bool only.
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        return self::verifyWithReason($rawBody, $request)['ok'];
    }

    /**
     * Lemon Squeezy webhook signing:
     * - Header: X-Signature
     * - Value: HMAC hex digest (sha256) over RAW request body using signing secret
     *
     * @return array{ok: bool, reason: string}
     */
    public static function verifyWithReason(string $rawBody, Request $request): array
    {
        $secret = config('tenrusl.ls_webhook_secret');
        if (! is_string($secret) || trim($secret) === '') {
            return self::result(false, 'missing_secret');
        }

        $signature = self::headerString($request, 'X-Signature');
        if ($signature === null) {
            return self::result(false, 'missing_signature_header');
        }

        $sigNorm = strtolower(trim($signature));
        if (str_starts_with($sigNorm, 'sha256=')) {
            $sigNorm = substr($sigNorm, 7);
        }

        $expected = hash_hmac('sha256', $rawBody, $secret); // hex lowercase

        if (hash_equals(strtolower($expected), $sigNorm)) {
            return self::result(true, 'ok');
        }

        return self::result(false, 'invalid_signature');
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
