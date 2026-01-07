<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

final class SkrillSignature
{
    /**
     * Backward-compatible: return bool only.
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        return self::verifyWithReason($rawBody, $request)['ok'];
    }

    /**
     * Skrill IPN/Status URL signature verification.
     *
     * md5sig (umum):
     *   MD5SIG = UPPERCASE(
     *     MD5( merchant_id . transaction_id(or mb_transaction_id) . UPPERCASE(MD5(secret_word)) . mb_amount . mb_currency . status )
     *   )
     *
     * Catatan kompatibilitas:
     * - Beberapa varian payload memakai `mb_transaction_id`, sebagian memakai `transaction_id`.
     *
     * sha2sig (opsional):
     * - Jika ada, biasanya SHA256 atas base string yang sama.
     *
     * @return array{ok: bool, reason: string}
     */
    public static function verifyWithReason(string $rawBody, Request $request): array
    {
        // Skrill umumnya POST application/x-www-form-urlencoded
        $params = [];
        parse_str($rawBody, $params);

        if (! is_array($params) || $params === []) {
            return self::result(false, 'invalid_form_body');
        }

        $merchantId = (string) ($params['merchant_id'] ?? '');
        // Prefer mb_transaction_id (lebih sering di dok), fallback ke transaction_id
        $transactionId = (string) ($params['mb_transaction_id'] ?? ($params['transaction_id'] ?? ''));
        $mbAmount = (string) ($params['mb_amount'] ?? '');
        $mbCurrency = (string) ($params['mb_currency'] ?? '');
        $status = (string) ($params['status'] ?? '');
        $md5Posted = (string) ($params['md5sig'] ?? '');
        $sha2Posted = (string) ($params['sha2sig'] ?? '');

        if ($md5Posted === '' && $sha2Posted === '') {
            return self::result(false, 'missing_signature');
        }

        $secretWord = config('tenrusl.skrill_md5_secret');
        if (! is_string($secretWord) || trim($secretWord) === '') {
            return self::result(false, 'missing_secret_word');
        }

        if ($merchantId === '' || $transactionId === '' || $mbAmount === '' || $mbCurrency === '' || $status === '') {
            return self::result(false, 'missing_fields');
        }

        $secretWordMd5Upper = strtoupper(md5($secretWord));
        $baseString = $merchantId.$transactionId.$secretWordMd5Upper.$mbAmount.$mbCurrency.$status;

        // Validate md5sig if present
        if ($md5Posted !== '') {
            $md5Calc = strtoupper(md5($baseString));
            if (! hash_equals($md5Calc, strtoupper($md5Posted))) {
                return self::result(false, 'invalid_md5sig');
            }
        }

        // Validate sha2sig if present
        if ($sha2Posted !== '') {
            $sha2Calc = hash('sha256', $baseString); // lowercase hex
            if (! hash_equals(strtolower($sha2Calc), strtolower($sha2Posted))) {
                return self::result(false, 'invalid_sha2sig');
            }
        }

        return self::result(true, 'ok');
    }

    /**
     * @return array{ok: bool, reason: string}
     */
    private static function result(bool $ok, string $reason): array
    {
        return ['ok' => $ok, 'reason' => $reason];
    }
}
