<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class AmazonBwpSignature
{
    /**
     * Backward-compatible: return bool only.
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        return self::verifyWithReason($rawBody, $request)['ok'];
    }

    /**
     * Buy with Prime webhook signature verification:
     * - Headers:
     *   - x-amzn-signature : base64 signature of the webhook payload (raw body)
     *   - x-amzn-kid       : key id to select public key from JWKS
     * - Algorithm: ECDSA_SHA_384 (ES384 / SHA384withECDSA)
     * - Public keys: JWKS at https://api.buywithprime.amazon.com/jwks.json (cache recommended)
     *
     * @return array{ok: bool, reason: string}
     */
    public static function verifyWithReason(string $rawBody, Request $request): array
    {
        $sigB64 = self::headerString($request, 'x-amzn-signature');
        $kid = self::headerString($request, 'x-amzn-kid');

        if ($sigB64 === null) {
            return self::result(false, 'missing_signature_header');
        }
        if ($kid === null) {
            return self::result(false, 'missing_kid_header');
        }

        $sig = base64_decode($sigB64, true);
        if ($sig === false) {
            return self::result(false, 'invalid_signature_base64');
        }

        $pem = self::getPublicKeyPemByKid($kid);
        if ($pem === null) {
            return self::result(false, 'public_key_unavailable');
        }

        $ok = @openssl_verify($rawBody, $sig, $pem, OPENSSL_ALGO_SHA384);

        if ($ok === 1) {
            return self::result(true, 'ok');
        }

        if ($ok === 0) {
            return self::result(false, 'invalid_signature');
        }

        // -1 or other -> openssl error
        return self::result(false, 'openssl_verify_error');
    }

    private static function getPublicKeyPemByKid(string $kid): ?string
    {
        $cacheKey = 'tenrusl:amzn_bwp:jwks_pem:'.hash('sha256', $kid);
        $cached = Cache::get($cacheKey);

        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        $jwksUrl = (string) config('tenrusl.amzn_bwp_jwks_url', 'https://api.buywithprime.amazon.com/jwks.json');
        $timeout = (int) config('tenrusl.amzn_bwp_jwks_timeout_seconds', 3);
        $retries = (int) config('tenrusl.amzn_bwp_jwks_retries', 1);
        $retryDelayMs = (int) config('tenrusl.amzn_bwp_jwks_retry_delay_ms', 200);
        $cacheSeconds = (int) config('tenrusl.amzn_bwp_jwks_cache_seconds', 86400);

        try {
            $resp = Http::acceptJson()
                ->timeout($timeout > 0 ? $timeout : 3)
                ->retry($retries >= 0 ? $retries : 1, $retryDelayMs >= 0 ? $retryDelayMs : 200, throw: false)
                ->get($jwksUrl);
        } catch (\Throwable $e) {
            Log::warning('amzn_bwp_jwks_fetch_exception', [
                'kid' => $kid,
                'exception' => $e::class,
            ]);

            return null;
        }

        if (! $resp->ok()) {
            Log::warning('amzn_bwp_jwks_fetch_failed', [
                'kid' => $kid,
                'status' => $resp->status(),
            ]);

            return null;
        }

        $jwks = $resp->json();
        if (! is_array($jwks) || ! isset($jwks['keys']) || ! is_array($jwks['keys'])) {
            return null;
        }

        $jwk = null;
        foreach ($jwks['keys'] as $key) {
            if (is_array($key) && isset($key['kid']) && (string) $key['kid'] === $kid) {
                $jwk = $key;
                break;
            }
        }

        if (! is_array($jwk)) {
            return null;
        }

        // Expect EC P-384 ES384 key
        $kty = (string) ($jwk['kty'] ?? '');
        $crv = (string) ($jwk['crv'] ?? '');
        $x = (string) ($jwk['x'] ?? '');
        $y = (string) ($jwk['y'] ?? '');

        if (strtoupper($kty) !== 'EC' || strtoupper($crv) !== 'P-384' || $x === '' || $y === '') {
            return null;
        }

        $xBin = self::base64UrlDecode($x);
        $yBin = self::base64UrlDecode($y);

        if ($xBin === null || $yBin === null) {
            return null;
        }

        // P-384 coordinates are 48 bytes each
        if (strlen($xBin) !== 48 || strlen($yBin) !== 48) {
            return null;
        }

        $uncompressedPoint = "\x04".$xBin.$yBin;

        $pem = self::spkiPemFromP384Point($uncompressedPoint);
        if ($pem === null) {
            return null;
        }

        Cache::put($cacheKey, $pem, $cacheSeconds > 0 ? $cacheSeconds : 86400);

        return $pem;
    }

    private static function base64UrlDecode(string $in): ?string
    {
        $b64 = str_replace(['-', '_'], ['+', '/'], $in);
        $pad = strlen($b64) % 4;
        if ($pad !== 0) {
            $b64 .= str_repeat('=', 4 - $pad);
        }

        $out = base64_decode($b64, true);

        return $out === false ? null : $out;
    }

    /**
     * Build SubjectPublicKeyInfo PEM for EC P-384 public key from uncompressed point.
     */
    private static function spkiPemFromP384Point(string $uncompressedPoint): ?string
    {
        // AlgorithmIdentifier: SEQUENCE( OID ecPublicKey, OID secp384r1 )
        // ecPublicKey OID: 1.2.840.10045.2.1 -> 06 07 2A 86 48 CE 3D 02 01
        // secp384r1 OID: 1.3.132.0.34 -> 06 05 2B 81 04 00 22

        $oidEcPublicKey = "\x06\x07\x2A\x86\x48\xCE\x3D\x02\x01";
        $oidSecp384r1 = "\x06\x05\x2B\x81\x04\x00\x22";

        $algId = self::derSequence($oidEcPublicKey.$oidSecp384r1);

        // BIT STRING: 0 unused bits + point bytes
        $bitString = self::derBitString("\x00".$uncompressedPoint);

        $spki = self::derSequence($algId.$bitString);

        $b64 = chunk_split(base64_encode($spki), 64, "\n");

        return "-----BEGIN PUBLIC KEY-----\n{$b64}-----END PUBLIC KEY-----\n";
    }

    private static function derSequence(string $content): string
    {
        return "\x30".self::derLength(strlen($content)).$content;
    }

    private static function derBitString(string $content): string
    {
        return "\x03".self::derLength(strlen($content)).$content;
    }

    private static function derLength(int $len): string
    {
        if ($len < 0) {
            $len = 0;
        }

        if ($len < 128) {
            return chr($len);
        }

        $bytes = '';
        $v = $len;
        while ($v > 0) {
            $bytes = chr($v & 0xFF).$bytes;
            $v >>= 8;
        }

        return chr(0x80 | strlen($bytes)).$bytes;
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
