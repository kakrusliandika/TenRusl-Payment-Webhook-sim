<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

final class MidtransSignature
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
        $serverKey = config('tenrusl.midtrans_server_key');
        if (!is_string($serverKey) || trim($serverKey) === '') {
            return self::result(false, 'missing_server_key');
        }

        $data = json_decode($rawBody, true);
        if (!is_array($data)) {
            return self::result(false, 'invalid_json');
        }

        $orderId = isset($data['order_id']) ? (string) $data['order_id'] : '';
        $statusCode = isset($data['status_code']) ? (string) $data['status_code'] : '';
        $grossAmount = isset($data['gross_amount']) ? (string) $data['gross_amount'] : '';
        $postedSig = isset($data['signature_key']) ? (string) $data['signature_key'] : '';

        if ($orderId === '' || $statusCode === '' || $grossAmount === '' || $postedSig === '') {
            return self::result(false, 'missing_fields');
        }

        // Midtrans formula:
        // signature_key = SHA512(order_id + status_code + gross_amount + serverKey)
        $calc = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if (hash_equals(strtolower($calc), strtolower($postedSig))) {
            return self::result(true, 'ok');
        }

        return self::result(false, 'invalid_signature');
    }

    /**
     * @return array{ok: bool, reason: string}
     */
    private static function result(bool $ok, string $reason): array
    {
        return ['ok' => $ok, 'reason' => $reason];
    }
}
