<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

final class DokuSignature
{
    /**
     * Verify DOKU HTTP Notification Signature.
     *
     * Komponen (per baris) mengikuti pola dokumentasi DOKU:
     *  Client-Id:{clientId}
     *  Request-Id:{requestId}
     *  Request-Timestamp:{timestamp}
     *  Request-Target:{requestTarget}
     *  [opsional] Digest:{base64(sha256(body))}
     *
     * Signature:
     *  "HMACSHA256=" + base64( HMAC_SHA256(secretKey, components) )
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        $secretKey = config('tenrusl.doku_secret_key');
        if (!is_string($secretKey) || $secretKey === '') {
            return false;
        }

        $clientId = self::headerString($request, 'Client-Id');
        $reqId = self::headerString($request, 'Request-Id');
        $timestamp = self::headerString($request, 'Request-Timestamp');
        $headerSig = self::headerString($request, 'Signature');

        if ($clientId === null || $reqId === null || $timestamp === null || $headerSig === null) {
            return false;
        }

        // Tentukan Request-Target:
        $target = config('tenrusl.doku_request_target', '/');
        $target = is_string($target) ? trim($target) : '/';

        if ($target === '' || $target === '/') {
            $uri = (string) $request->getRequestUri();
            $uri = $uri !== '' ? $uri : '/';
            $target = str_starts_with($uri, '/') ? $uri : '/' . ltrim($uri, '/');
        }

        $method = strtoupper((string) $request->getMethod());
        $includeDigest = !in_array($method, ['GET', 'DELETE'], true);

        $components = "Client-Id:{$clientId}\n"
            . "Request-Id:{$reqId}\n"
            . "Request-Timestamp:{$timestamp}\n"
            . "Request-Target:{$target}";

        if ($includeDigest) {
            $digestB64 = base64_encode(hash('sha256', $rawBody, true));
            $components .= "\nDigest:{$digestB64}";
        }

        $calc = 'HMACSHA256=' . base64_encode(hash_hmac('sha256', $components, $secretKey, true));

        return hash_equals($headerSig, $calc);
    }

    private static function headerString(Request $request, string $key): ?string
    {
        $v = $request->header($key);

        if (!is_string($v)) {
            return null;
        }

        $v = trim($v);

        return $v !== '' ? $v : null;
    }
}
