<?php

declare(strict_types=1);

namespace App\Services\Signatures;

use Illuminate\Http\Request;

final class PaddleSignature
{
    /**
     * Backward-compatible: return bool only.
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        return self::verifyWithReason($rawBody, $request)['ok'];
    }

    /**
     * Verifies Paddle webhook signature.
     *
     * Supported modes:
     * 1) Paddle Billing (modern):
     *    - Header: "Paddle-Signature" (contoh: "ts=1671552777;h1=abcdef...")
     *    - Signature: HMAC-SHA256(secret, "{$ts}:{$rawBody}") dibandingkan dengan h1 (dan h2, dst jika ada).
     *    - Timestamp tolerance check untuk anti-replay.
     *
     * 2) Paddle Classic (legacy):
     *    - form POST dengan field "p_signature" (RSA, public key).
     *
     * @return array{ok: bool, reason: string}
     */
    public static function verifyWithReason(string $rawBody, Request $request): array
    {
        $header = self::headerString($request, 'Paddle-Signature')
            ?? self::headerString($request, 'paddle-signature');

        if ($header !== null) {
            return self::verifyBillingHmacWithReason($rawBody, $header);
        }

        return self::verifyClassicRsaWithReason($rawBody);
    }

    /**
     * Paddle Billing:
     * - Parse header: "ts=...;h1=...;h2=..."
     * - Build signed payload: "{$ts}:{$rawBody}"
     * - expected = HMAC-SHA256(secret, signedPayload) (hex)
     *
     * @return array{ok: bool, reason: string}
     */
    private static function verifyBillingHmacWithReason(string $rawBody, string $paddleSignatureHeader): array
    {
        $secret = config('tenrusl.paddle_signing_secret');
        if (! is_string($secret) || trim($secret) === '') {
            return self::result(false, 'missing_secret');
        }

        // Parse header like: "ts=1700000000; h1=abcdef...; h2=..."
        // Some environments may use comma separators, so accept both.
        $items = preg_split('/[;,]+/', $paddleSignatureHeader) ?: [];
        $parts = [];

        foreach ($items as $item) {
            $item = trim((string) $item);
            if ($item === '') {
                continue;
            }

            $kv = explode('=', $item, 2);
            if (count($kv) !== 2) {
                continue;
            }

            $k = strtolower(trim($kv[0]));
            $v = trim($kv[1]);

            if ($k !== '' && $v !== '') {
                $parts[$k] = $v;
            }
        }

        $ts = $parts['ts'] ?? null;
        if (! is_string($ts) || $ts === '' || ! ctype_digit($ts)) {
            return self::result(false, 'missing_or_invalid_ts');
        }

        // Collect all h* signatures (h1, h2, ...)
        $provided = [];
        foreach ($parts as $k => $v) {
            if ($k === 'ts') {
                continue;
            }
            if (str_starts_with($k, 'h')) {
                $provided[] = strtolower((string) $v);
            }
        }

        if ($provided === []) {
            return self::result(false, 'missing_hmac');
        }

        // Anti-replay timestamp tolerance (seconds)
        $tolerance = (int) config('tenrusl.signature.timestamp_leeway_seconds', 300);
        $tolerance = $tolerance < 0 ? 0 : $tolerance;

        $now = time();
        $tsInt = (int) $ts;

        if ($tsInt <= 0) {
            return self::result(false, 'invalid_ts_value');
        }

        if (abs($now - $tsInt) > $tolerance) {
            return self::result(false, 'timestamp_out_of_tolerance');
        }

        // IMPORTANT: must use RAW request body bytes
        $signedPayload = $ts.':'.$rawBody;
        $expected = hash_hmac('sha256', $signedPayload, $secret); // hex lowercase

        foreach ($provided as $sig) {
            $sigNorm = strtolower(trim($sig));
            if (str_starts_with($sigNorm, 'sha256=')) {
                $sigNorm = substr($sigNorm, 7);
            }

            if (hash_equals(strtolower($expected), $sigNorm)) {
                return self::result(true, 'ok_billing_hmac');
            }
        }

        return self::result(false, 'invalid_signature');
    }

    /**
     * Paddle Classic: verify RSA signature in `p_signature` (form POST).
     *
     * @return array{ok: bool, reason: string}
     */
    private static function verifyClassicRsaWithReason(string $rawBody): array
    {
        $publicKey = config('tenrusl.paddle_public_key');
        if (! is_string($publicKey) || trim($publicKey) === '') {
            return self::result(false, 'missing_public_key');
        }

        $fields = [];
        parse_str($rawBody, $fields);

        if (! is_array($fields) || $fields === []) {
            return self::result(false, 'invalid_form_body');
        }

        $pSignature = $fields['p_signature'] ?? null;
        if (! is_string($pSignature) || trim($pSignature) === '') {
            return self::result(false, 'missing_p_signature');
        }

        unset($fields['p_signature']);
        ksort($fields);

        // Paddle classic uses PHP serialization of sorted fields
        $data = serialize($fields);

        $binarySignature = base64_decode($pSignature, true);
        if ($binarySignature === false) {
            return self::result(false, 'invalid_p_signature_base64');
        }

        $pem = self::normalizePem($publicKey);

        // Legacy Paddle signatures often verify with SHA1 in older integrations.
        $ok = @openssl_verify($data, $binarySignature, $pem, OPENSSL_ALGO_SHA1);

        if ($ok === 1) {
            return self::result(true, 'ok_classic_rsa');
        }

        if ($ok === 0) {
            return self::result(false, 'invalid_signature');
        }

        return self::result(false, 'openssl_verify_error');
    }

    private static function normalizePem(string $key): string
    {
        $trim = trim($key);

        // Accept both BEGIN PUBLIC KEY and BEGIN RSA PUBLIC KEY blocks as-is.
        if (str_contains($trim, 'BEGIN PUBLIC KEY') || str_contains($trim, 'BEGIN RSA PUBLIC KEY')) {
            return $trim;
        }

        // If it looks like base64, wrap as PUBLIC KEY.
        $wrapped = chunk_split(str_replace(["\r", "\n", ' '], '', $trim), 64, "\n");

        return "-----BEGIN PUBLIC KEY-----\n{$wrapped}-----END PUBLIC KEY-----\n";
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
