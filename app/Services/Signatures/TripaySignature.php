<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

final class TripaySignature
{
    /**
     * Backward-compatible: return bool only.
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        return self::verifyWithReason($rawBody, $request)['ok'];
    }

    /**
     * TriPay callback signature:
     * - Header: X-Callback-Signature
     * - Value: hex(HMAC-SHA256(rawBody, private_key))
     *
     * TriPay docs show callback signature is HMAC-SHA256 keyed with merchant private key. :contentReference[oaicite:5]{index=5}
     *
     * @return array{ok: bool, reason: string}
     */
    public static function verifyWithReason(string $rawBody, Request $request): array
    {
        $privateKey = config('tenrusl.tripay_private_key');
        if (!is_string($privateKey) || trim($privateKey) === '') {
            return self::result(false, 'missing_private_key');
        }

        $headerSig = self::headerString($request, 'X-Callback-Signature');
        if ($headerSig === null) {
            return self::result(false, 'missing_signature_header');
        }

        $sigNorm = strtolower(trim($headerSig));
        if (str_starts_with($sigNorm, 'sha256=')) {
            $sigNorm = substr($sigNorm, 7);
        }

        $expected = strtolower(hash_hmac('sha256', $rawBody, $privateKey));

        if (hash_equals($expected, $sigNorm)) {
            return self::result(true, 'ok');
        }

        return self::result(false, 'invalid_signature');
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
