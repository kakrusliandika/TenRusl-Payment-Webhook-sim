<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

final class DokuSignature
{
    /**
     * Backward-compatible: return bool only.
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        return self::verifyWithReason($rawBody, $request)['ok'];
    }

    /**
     * Verify DOKU HTTP Notification Signature.
     *
     * Components (one per line, no trailing \n):
     *  Client-Id:{clientId}
     *  Request-Id:{requestId}
     *  Request-Timestamp:{timestamp}
     *  Request-Target:{requestTarget}
     *  Digest:{base64(sha256(body))}  // for non-GET/DELETE
     *
     * Signature:
     *  "HMACSHA256=" + base64( HMAC_SHA256(secretKey, components) )
     *
     * @return array{ok: bool, reason: string}
     */
    public static function verifyWithReason(string $rawBody, Request $request): array
    {
        $secretKey = config('tenrusl.doku_secret_key');
        if (! is_string($secretKey) || trim($secretKey) === '') {
            return self::result(false, 'missing_secret_key');
        }

        $clientId = self::headerString($request, 'Client-Id');
        $reqId = self::headerString($request, 'Request-Id');
        $timestamp = self::headerString($request, 'Request-Timestamp');
        $headerSig = self::headerString($request, 'Signature');

        if ($clientId === null || $reqId === null || $timestamp === null || $headerSig === null) {
            return self::result(false, 'missing_headers');
        }

        // Determine Request-Target (path only). Prefer config override.
        $target = config('tenrusl.doku_request_target', '/');
        $target = is_string($target) ? trim($target) : '/';

        if ($target === '' || $target === '/') {
            $uri = (string) $request->getRequestUri(); // may include query
            $path = parse_url($uri, PHP_URL_PATH);
            $path = is_string($path) && $path !== '' ? $path : '/';
            $target = str_starts_with($path, '/') ? $path : '/'.ltrim($path, '/');
        }

        $method = strtoupper((string) $request->getMethod());
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

        if (hash_equals($headerSig, $calc)) {
            return self::result(true, 'ok');
        }

        return self::result(false, 'invalid_signature');
    }

    private static function headerString(Request $request, string $key): ?string
    {
        $v = $request->headers->get($key);
        if (! is_string($v)) {
            return null;
        }

        $v = trim($v);

        return $v !== '' ? $v : null;
    }

    /**
     * @return array{ok: bool, reason: string}
     */
    private static function result(bool $ok, string $reason): array
    {
        return ['ok' => $ok, 'reason' => $reason];
    }
}
