<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

final class XenditSignature
{
    /**
     * Xendit webhook verification:
     * - Token ada di header `x-callback-token` (atau variasi kapitalisasi)
     * - Cocokkan dengan token di config/env kamu.
     *
     * Catatan: $rawBody tidak dipakai untuk token-based verification,
     * tapi dipertahankan agar signature interface konsisten.
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        $expected = config('tenrusl.xendit_callback_token');
        if (!is_string($expected) || trim($expected) === '') {
            return false;
        }

        $token = self::headerString($request, 'x-callback-token')
            ?? self::headerString($request, 'X-CALLBACK-TOKEN')
            ?? self::headerString($request, 'X-Callback-Token');

        if ($token === null) {
            return false;
        }

        return hash_equals(trim($expected), $token);
    }

    private static function headerString(Request $request, string $key): ?string
    {
        // Symfony HeaderBag::get($key, $default = null) => ?string
        $v = $request->headers->get($key);

        if ($v === null) {
            return null;
        }

        $v = trim($v);

        return $v !== '' ? $v : null;
    }
}
