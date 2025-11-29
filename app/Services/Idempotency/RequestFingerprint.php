<?php

declare(strict_types=1);

namespace App\Services\Idempotency;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Membuat fingerprint stabil dari isi request (method+path+headers penting+payload).
 * Dipakai saat client tidak mengirim "Idempotency-Key".
 */
final class RequestFingerprint
{
    /**
     * Hasilkan hash hex (sha256) dari representasi kanonik request.
     */
    public function hash(Request $request): string
    {
        $canonical = $this->canonical($request);

        return hash('sha256', $canonical);
    }

    /**
     * Bentuk string kanonik yang stabil:
     *   METHOD \n PATH \n content-type \n accept \n body-json-terurut|raw
     */
    public function canonical(Request $request): string
    {
        $method = strtoupper($request->getMethod());
        $path = '/'.ltrim($request->getRequestUri() ?: '/', '/');

        $ct = strtolower((string) $request->header('content-type', ''));
        $accept = strtolower((string) $request->header('accept', ''));

        $raw = (string) $request->getContent();

        $body = $this->canonicalizeJson($raw, $ct);

        return implode("\n", [$method, $path, $ct, $accept, $body]);
    }

    /**
     * Jika content-type JSON, urutkan key secara rekursif lalu encode tanpa whitespace;
     * jika bukan, kembalikan raw body apa adanya (agar cocok skema signature sebagian provider).
     */
    private function canonicalizeJson(string $rawBody, string $ct): string
    {
        if (! Str::contains($ct, 'application/json')) {
            return $rawBody;
        }

        $decoded = json_decode($rawBody, true);
        if ($decoded === null || ! is_array($decoded)) {
            return $rawBody; // bukan JSON valid, pakai raw
        }

        $sorted = $this->ksortRecursive($decoded);

        return json_encode($sorted, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private function ksortRecursive(array $arr): array
    {
        ksort($arr);
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $arr[$k] = $this->ksortRecursive($v);
            }
        }

        return $arr;
    }
}
