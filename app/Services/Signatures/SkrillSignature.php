<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

final class SkrillSignature
{
    /**
     * Verifikasi IPN/Status URL Skrill.
     *
     * md5sig = UPPERCASE(
     *   MD5( merchant_id . transaction_id . UPPERCASE(MD5(secret_word)) . mb_amount . mb_currency . status )
     * )
     *
     * sha2sig (opsional) dibentuk sama, namun menggunakan SHA-256 (over string yang sama).
     *
     * Catatan keamanan:
     * - Minimal salah satu signature (md5sig/sha2sig) harus ada.
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        // Skrill mem-post application/x-www-form-urlencoded
        $params = [];
        parse_str($rawBody, $params); // $params pasti array

        if (empty($params)) {
            return false;
        }

        $merchantId     = (string) ($params['merchant_id'] ?? '');
        $transactionId  = (string) ($params['transaction_id'] ?? '');
        $mbAmount       = (string) ($params['mb_amount'] ?? '');
        $mbCurrency     = (string) ($params['mb_currency'] ?? '');
        $status         = (string) ($params['status'] ?? '');
        $md5sigPosted   = (string) ($params['md5sig'] ?? '');
        $sha2Posted     = (string) ($params['sha2sig'] ?? '');

        // Harus ada minimal salah satu signature
        if ($md5sigPosted === '' && $sha2Posted === '') {
            return false;
        }

        $secretWord = config('tenrusl.skrill_md5_secret');
        if (!is_string($secretWord) || $secretWord === '') {
            return false;
        }

        // Field inti harus ada untuk membentuk payload string
        if ($merchantId === '' || $transactionId === '' || $mbAmount === '' || $mbCurrency === '' || $status === '') {
            return false;
        }

        // Build signature string per dokumentasi Skrill
        $secretWordMd5Upper = strtoupper(md5($secretWord));
        $baseString = $merchantId . $transactionId . $secretWordMd5Upper . $mbAmount . $mbCurrency . $status;

        // Validasi md5sig jika ada
        if ($md5sigPosted !== '') {
            $md5Calc = strtoupper(md5($baseString));
            if (!hash_equals($md5Calc, strtoupper($md5sigPosted))) {
                return false;
            }
        }

        // Validasi sha2sig jika ada
        if ($sha2Posted !== '') {
            $sha2Calc = hash('sha256', $baseString);
            if (!hash_equals(strtolower($sha2Calc), strtolower($sha2Posted))) {
                return false;
            }
        }

        return true;
    }
}
