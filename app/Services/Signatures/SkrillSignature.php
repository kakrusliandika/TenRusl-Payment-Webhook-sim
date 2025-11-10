<?php

namespace App\Services\Signatures;

use Illuminate\Http\Request;

class SkrillSignature
{
    /**
     * Verifikasi IPN/Status URL Skrill.
     *
     * md5sig = UPPERCASE(
     *   MD5( merchant_id . transaction_id . UPPERCASE(MD5(secret_word)) . mb_amount . mb_currency . status )
     * )
     *
     * sha2sig (opsional) dibentuk sama, namun menggunakan SHA-256.
     * Penting: gunakan nilai persis seperti yang dipost kembali oleh Skrill (jangan di-normalisasi).
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        // Skrill mem-post application/x-www-form-urlencoded
        $params = [];
        parse_str($rawBody, $params);
        if (!is_array($params) || empty($params)) {
            return false;
        }

        $merchantId   = (string) ($params['merchant_id'] ?? '');
        $transactionId= (string) ($params['transaction_id'] ?? '');
        $mbAmount     = (string) ($params['mb_amount'] ?? '');
        $mbCurrency   = (string) ($params['mb_currency'] ?? '');
        $status       = (string) ($params['status'] ?? '');
        $md5sigPosted = (string) ($params['md5sig'] ?? '');
        $sha2Posted   = (string) ($params['sha2sig'] ?? '');

        $secretWord   = (string) config('tenrusl.skrill_md5_secret'); // "secret word" di Settings > Developer Settings
        if ($secretWord === '' || $secretWord === null) {
            return false;
        }

        // Build MD5 signature per dokumentasi resmi
        $secretWordMd5Upper = strtoupper(md5($secretWord));
        $md5String = $merchantId . $transactionId . $secretWordMd5Upper . $mbAmount . $mbCurrency . $status;
        $md5Calc   = strtoupper(md5($md5String));

        // Jika md5sig ada, wajib cocok
        if ($md5sigPosted !== '') {
            if (!hash_equals($md5Calc, strtoupper($md5sigPosted))) {
                return false;
            }
        }

        // Jika sha2sig juga dipost, validasi juga (dibentuk sama namun pakai SHA-256; case-insensitive compare)
        if ($sha2Posted !== '') {
            $sha2Calc = hash('sha256', $md5String);
            if (!hash_equals(strtolower($sha2Calc), strtolower($sha2Posted))) {
                return false;
            }
        }

        return true;
    }
}
