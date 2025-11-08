<?php

namespace App\Services\Idempotency;

/**
 * Membuat fingerprint stabil dari request untuk keperluan idempotensi
 * (kombinasi method + path + body terkanonisasi).
 */
class RequestFingerprint
{
    /**
     * @param string $method  e.g., POST
     * @param string $path    e.g., /api/v1/payments
     * @param array|string|null $body
     */
    public function hash(string $method, string $path, $body = null): string
    {
        $canonical = $this->canonicalize($body);
        return hash('sha256', strtoupper($method) . '|' . $path . '|' . $canonical);
    }

    protected function canonicalize($body): string
    {
        if ($body === null) {
            return '';
        }

        if (is_string($body)) {
            return $body;
        }

        if (is_array($body)) {
            // sort keys recursively
            $arr = $this->ksortRecursive($body);
            return json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return (string) $body;
    }

    protected function ksortRecursive(array $arr): array
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
