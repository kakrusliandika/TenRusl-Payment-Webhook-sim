<?php

namespace App\Services\Signatures;

use Illuminate\Http\Request;

class DokuSignature
{
    /**
     * Verify DOKU HTTP Notification Signature.
     *
     * Komponen yang dicat (per baris):
     *  Client-Id:{clientId}
     *  Request-Id:{requestId}
     *  Request-Timestamp:{timestamp}
     *  Request-Target:{requestTarget}
     *  [opsional] Digest:{base64(sha256(body))}
     *
     * Lalu: Signature = "HMACSHA256=" + base64( HMAC_SHA256(secretKey, components) )
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        $clientId = (string) $request->header('Client-Id');
        $reqId = (string) $request->header('Request-Id');
        $timestamp = (string) $request->header('Request-Timestamp');
        $headerSig = (string) $request->header('Signature');

        $secretKey = (string) config('tenrusl.doku_secret_key');
        if ($secretKey === '' || $secretKey === null) {
            return false;
        }

        // Tentukan Request-Target: pakai config simulasi bila ada, fallback ke URI request
        $target = (string) config('tenrusl.doku_request_target', '/');
        if ($target === '/' || $target === '') {
            $uri = $request->getRequestUri();
            $target = ($uri && str_starts_with($uri, '/')) ? $uri : '/'.ltrim((string) $uri, '/');
        }

        if ($clientId === '' || $reqId === '' || $timestamp === '' || $headerSig === '') {
            return false;
        }

        // Digest hanya diperlukan bila ada body & method bukan GET/DELETE
        $method = strtoupper($request->getMethod());
        $includeDigest = ! in_array($method, ['GET', 'DELETE'], true);

        $components = "Client-Id:{$clientId}\n"
            ."Request-Id:{$reqId}\n"
            ."Request-Timestamp:{$timestamp}\n"
            ."Request-Target:{$target}";

        if ($includeDigest) {
            $digestB64 = base64_encode(hash('sha256', $rawBody, true));
            $components .= "\nDigest:{$digestB64}";
        }

        $calc = 'HMACSHA256='.base64_encode(hash_hmac('sha256', $components, $secretKey, true));

        return hash_equals((string) $headerSig, (string) $calc);
    }
}
