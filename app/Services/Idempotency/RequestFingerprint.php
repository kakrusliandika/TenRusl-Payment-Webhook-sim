<?php

// app/Services/Idempotency/RequestFingerprint.php

declare(strict_types=1);

namespace App\Services\Idempotency;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Membuat fingerprint stabil dari isi request (method + path + headers penting + payload).
 * Dipakai saat client tidak mengirim "Idempotency-Key".
 *
 * Tujuan:
 * - Konsisten untuk request "yang sama" (secara semantik),
 * - Mismatch parameter terdeteksi via hash yang berbeda.
 */
final class RequestFingerprint
{
    /**
     * Hasilkan hash hex (sha256) dari representasi kanonik request.
     */
    public function hash(Request $request): string
    {
        return hash('sha256', $this->canonical($request));
    }

    /**
     * Bentuk string kanonik yang stabil:
     *   METHOD \n URI \n content-type \n accept \n body(json-kanonik|raw)
     */
    public function canonical(Request $request): string
    {
        $method = strtoupper((string) $request->getMethod());

        // getRequestUri() termasuk query string (kalau ada) => semantik bisa beda
        $uri = (string) ($request->getRequestUri() ?: '/');
        $uri = '/'.ltrim($uri, '/');

        $ct = strtolower((string) $request->header('content-type', ''));
        $accept = strtolower((string) $request->header('accept', ''));

        $raw = (string) $request->getContent();

        $body = $this->canonicalizeBody($raw, $ct);

        return implode("\n", [$method, $uri, $ct, $accept, $body]);
    }

    /**
     * Jika JSON: urutkan key object secara rekursif dan encode tanpa whitespace.
     * Jika bukan JSON: kembalikan raw body apa adanya.
     */
    private function canonicalizeBody(string $rawBody, string $contentType): string
    {
        if ($rawBody === '') {
            return '';
        }

        if (! Str::contains($contentType, 'application/json')) {
            return $rawBody;
        }

        $decoded = json_decode($rawBody, true);
        if (! is_array($decoded)) {
            return $rawBody; // bukan JSON valid -> pakai raw
        }

        $sorted = $this->sortJson($decoded);

        $json = json_encode(
            $sorted,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION
        );

        return is_string($json) ? $json : $rawBody;
    }

    /**
     * Sort JSON "object" keys recursively. Keep list-array order intact.
     *
     * @param  array<mixed>  $value
     * @return array<mixed>
     */
    private function sortJson(array $value): array
    {
        if ($this->isAssoc($value)) {
            ksort($value);
        }

        foreach ($value as $k => $v) {
            if (is_array($v)) {
                $value[$k] = $this->sortJson($v);
            }
        }

        return $value;
    }

    /**
     * @param  array<mixed>  $arr
     */
    private function isAssoc(array $arr): bool
    {
        $keys = array_keys($arr);
        $n = count($keys);

        for ($i = 0; $i < $n; $i++) {
            if ($keys[$i] !== $i) {
                return true;
            }
        }

        return false;
    }
}
