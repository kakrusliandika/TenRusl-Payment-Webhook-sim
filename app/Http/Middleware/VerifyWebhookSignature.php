<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Signatures\SignatureVerifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * VerifyWebhookSignature
 * ----------------------
 * Gate sebelum domain logic:
 * - Ambil {provider} dari route /webhooks/{provider}
 * - Validasi request-level policy (method, content-type, size limit)
 * - Baca RAW body (bukan hasil decode)
 * - Simpan RAW body ke request attribute 'tenrusl_raw_body'
 * - Validasi signature-material wajib (header / body field) per provider
 * - Simpan fingerprint signature (hash) ke request attribute (untuk audit/persist)
 * - Verifikasi signature via SignatureVerifier
 * - Replay mitigation (timestamp tolerance) untuk provider yang mendukung
 * - Kalau gagal: stop dan return error JSON generik (tanpa bocor detail verifikasi).
 */
class VerifyWebhookSignature
{
    public const RAW_BODY_ATTR = 'tenrusl_raw_body';

    public const SIG_HASH_ATTR = 'tenrusl_signature_hash';

    public const SIG_SOURCE_ATTR = 'tenrusl_signature_source';

    public const SIG_VERIFIED_ATTR = 'tenrusl_signature_verified';

    /**
     * Optional DI: jika SignatureVerifier kamu dibuat sebagai service instance.
     * Kalau tidak ada, middleware akan fallback ke static SignatureVerifier::verify(...).
     */
    public function __construct(private readonly ?SignatureVerifier $verifier = null) {}

    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, \Closure $next): Response
    {
        // Preflight (biasanya OPTIONS route tidak dipasang middleware ini)
        if ($request->isMethod('OPTIONS')) {
            return response()->noContent(Response::HTTP_NO_CONTENT);
        }

        if (! $request->isMethod('POST')) {
            return $this->errorGeneric(
                status: Response::HTTP_METHOD_NOT_ALLOWED,
                code: 'method_not_allowed',
                message: 'Method not allowed.'
            );
        }

        $provider = strtolower((string) $request->route('provider'));
        if ($provider === '') {
            // ini bukan signature issue, tapi routing issue
            return $this->errorGeneric(
                status: Response::HTTP_UNAUTHORIZED,
                code: 'unauthorized',
                message: 'Unauthorized.'
            );
        }

        // Enforce allowlist (deny-by-default jika list kosong)
        $allowlist = $this->normalizeAllowlist((array) config('tenrusl.providers_allowlist', []));
        if ($allowlist === [] || ! in_array($provider, $allowlist, true)) {
            $this->auditLog('failed', $request, $provider, [
                'reason' => 'provider_not_allowed',
            ]);

            return $this->errorGeneric(
                status: Response::HTTP_UNAUTHORIZED,
                code: 'unauthorized',
                message: 'Unauthorized.'
            );
        }

        // Enforce Content-Type
        $contentType = $this->normalizeContentType($request);
        if ($contentType === '' || ! $this->isAllowedContentType($provider, $contentType)) {
            $this->auditLog('failed', $request, $provider, [
                'reason' => 'unsupported_content_type',
                'content_type' => $contentType,
            ]);

            // Ini bukan “signature detail”, aman untuk dikembalikan lebih spesifik
            return $this->errorGeneric(
                status: Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                code: 'unsupported_media_type',
                message: 'Unsupported Content-Type.'
            );
        }

        // Payload size guard (sinkronkan dengan Nginx client_max_body_size)
        $maxBytes = $this->maxPayloadBytes();
        $contentLengthHeader = (string) ($request->headers->get('Content-Length') ?? '');

        if ($contentLengthHeader !== '' && ctype_digit($contentLengthHeader)) {
            $declared = (int) $contentLengthHeader;
            if ($declared > $maxBytes) {
                $this->auditLog('failed', $request, $provider, [
                    'reason' => 'payload_too_large',
                    'declared_bytes' => $declared,
                    'max_bytes' => $maxBytes,
                ]);

                return $this->errorGeneric(
                    status: Response::HTTP_REQUEST_ENTITY_TOO_LARGE,
                    code: 'payload_too_large',
                    message: 'Payload too large.'
                );
            }
        }

        // Ambil RAW body (source-of-truth untuk signature).
        // getContent() aman dipanggil dan nilainya dicache oleh Request.
        $rawBody = (string) $request->getContent();
        $rawBytes = strlen($rawBody);

        // Simpan raw body untuk downstream (controller/FormRequest)
        $request->attributes->set(self::RAW_BODY_ATTR, $rawBody);

        if ($rawBytes === 0) {
            $this->auditLog('failed', $request, $provider, [
                'reason' => 'empty_payload',
            ]);

            return $this->errorGeneric(
                status: Response::HTTP_BAD_REQUEST,
                code: 'invalid_payload',
                message: 'Invalid payload.'
            );
        }

        if ($rawBytes > $maxBytes) {
            $this->auditLog('failed', $request, $provider, [
                'reason' => 'payload_too_large',
                'payload_bytes' => $rawBytes,
                'max_bytes' => $maxBytes,
            ]);

            return $this->errorGeneric(
                status: Response::HTTP_REQUEST_ENTITY_TOO_LARGE,
                code: 'payload_too_large',
                message: 'Payload too large.'
            );
        }

        // Strict mode: production default lebih ketat.
        $strict = $this->strictMode();

        // Validasi signature-material wajib (header / body field) per provider
        $sig = $this->extractSignatureFingerprint($provider, $request, $rawBody);

        // Simpan fingerprint untuk audit/persist (tanpa bocor secret)
        if ($sig['hash'] !== null) {
            $request->attributes->set(self::SIG_HASH_ATTR, $sig['hash']);
        }
        if ($sig['source'] !== null) {
            $request->attributes->set(self::SIG_SOURCE_ATTR, $sig['source']);
        }

        if (! $sig['present']) {
            $this->auditLog('failed', $request, $provider, [
                'reason' => 'missing_signature_material',
                'signature_source' => $sig['source'],
                'signature_hash' => $sig['hash'],
            ]);

            return $this->errorGeneric(
                status: Response::HTTP_UNAUTHORIZED,
                code: 'unauthorized',
                message: 'Unauthorized.'
            );
        }

        // Replay mitigation (timestamp tolerance) untuk provider yang mendukung.
        // Di strict mode (production default), jika provider mensyaratkan timestamp,
        // maka timestamp harus ada & bisa diparse.
        if (! $this->passesReplayMitigation($provider, $request, $strict)) {
            $this->auditLog('failed', $request, $provider, [
                'reason' => 'replay_suspected',
                'signature_source' => $sig['source'],
                'signature_hash' => $sig['hash'],
            ]);

            return $this->errorGeneric(
                status: Response::HTTP_UNAUTHORIZED,
                code: 'unauthorized',
                message: 'Unauthorized.'
            );
        }

        // Verifikasi signature
        try {
            $verified = $this->verifySignature($provider, $rawBody, $request);
        } catch (\Throwable $e) {
            // Jangan bocorkan detail error/verifier ke client
            $this->auditLog('failed', $request, $provider, [
                'reason' => 'signature_verification_exception',
                'signature_source' => $sig['source'],
                'signature_hash' => $sig['hash'],
                'exception' => $e::class,
            ]);

            Log::warning('Webhook signature verification exception', [
                'provider' => $provider,
                'request_id' => $this->requestId($request),
                'exception' => $e::class,
            ]);

            return $this->errorGeneric(
                status: Response::HTTP_UNAUTHORIZED,
                code: 'unauthorized',
                message: 'Unauthorized.'
            );
        }

        if (! $verified) {
            $this->auditLog('failed', $request, $provider, [
                'reason' => 'invalid_signature',
                'signature_source' => $sig['source'],
                'signature_hash' => $sig['hash'],
            ]);

            return $this->errorGeneric(
                status: Response::HTTP_UNAUTHORIZED,
                code: 'unauthorized',
                message: 'Unauthorized.'
            );
        }

        $request->attributes->set(self::SIG_VERIFIED_ATTR, true);

        $this->auditLog('ok', $request, $provider, [
            'signature_source' => $sig['source'],
            'signature_hash' => $sig['hash'],
        ]);

        return $next($request);
    }

    private function strictMode(): bool
    {
        $cfg = config('tenrusl.signature.strict');
        if (is_bool($cfg)) {
            return $cfg;
        }

        $env = strtolower((string) config('app.env', 'production'));

        return $env === 'production';
    }

    /**
     * Verifikasi signature via:
     * - instance verifier (jika ada + punya method verify)
     * - fallback static SignatureVerifier::verify
     */
    private function verifySignature(string $provider, string $rawBody, Request $request): bool
    {
        if ($this->verifier !== null && method_exists($this->verifier, 'verify')) {
            /** @var bool $ok */
            $ok = $this->verifier->verify($provider, $rawBody, $request);

            return $ok;
        }

        if (is_callable([SignatureVerifier::class, 'verify'])) {
            /** @var bool $ok */
            $ok = SignatureVerifier::verify($provider, $rawBody, $request);

            return $ok;
        }

        throw new \RuntimeException('SignatureVerifier verify method is not available.');
    }

    /**
     * Normalize allowlist ke lowercase unique.
     *
     * @param  array<int, mixed>  $allow
     * @return string[]
     */
    private function normalizeAllowlist(array $allow): array
    {
        $out = [];

        foreach ($allow as $p) {
            $v = strtolower(trim((string) $p));
            if ($v !== '') {
                $out[] = $v;
            }
        }

        $out = array_values(array_unique($out));
        sort($out);

        return $out;
    }

    /**
     * Content-Type normalizer (ambil media-type saja, buang parameter seperti charset).
     */
    private function normalizeContentType(Request $request): string
    {
        $ct = $request->headers->get('Content-Type');

        if (! is_string($ct) || trim($ct) === '') {
            $ct = (string) (
                $request->server('CONTENT_TYPE')
                ?? $request->server('HTTP_CONTENT_TYPE')
                ?? ''
            );
        }

        $ct = strtolower(trim((string) $ct));
        if ($ct === '') {
            return '';
        }

        // Keep only media type (before ";")
        $parts = explode(';', $ct, 2);

        return trim($parts[0]);
    }

    /**
     * Provider-specific Content-Type policy.
     *
     * @return array{0: string, 1?: string}
     */
    private function allowedKindsForProvider(string $provider): array
    {
        return match ($provider) {
            // form-encoded providers
            'skrill' => ['form'],

            // supports json or classic form
            'paddle' => ['json', 'form'],

            // default json
            default => ['json'],
        };
    }

    private function isAllowedContentType(string $provider, string $contentType): bool
    {
        $kinds = $this->allowedKindsForProvider($provider);

        foreach ($kinds as $k) {
            if ($k === 'json' && $this->isJsonContentType($contentType)) {
                return true;
            }

            if ($k === 'form' && $contentType === 'application/x-www-form-urlencoded') {
                return true;
            }
        }

        return false;
    }

    /**
     * JSON media-type detector:
     * - application/json
     * - application/*+json (mis. application/vnd.api+json)
     */
    private function isJsonContentType(string $contentType): bool
    {
        if ($contentType === 'application/json') {
            return true;
        }

        return str_ends_with($contentType, '+json');
    }

    /**
     * Global payload size limit (bytes).
     *
     * - Prefer config() agar aman untuk config:cache
     * - Fallback getenv untuk external env (bukan .env runtime) sebagai safety net
     */
    private function maxPayloadBytes(): int
    {
        $v = config('tenrusl.webhook_max_payload_bytes');

        if (is_numeric($v) && (int) $v > 0) {
            return (int) $v;
        }

        $env = getenv('TENRUSL_WEBHOOK_MAX_PAYLOAD_BYTES');

        if (is_string($env) && ctype_digit($env) && (int) $env > 0) {
            return (int) $env;
        }

        // Default 1 MiB
        return 1024 * 1024;
    }

    /**
     * Timestamp tolerance (seconds) untuk mitigasi replay.
     *
     * - Prefer config() agar aman untuk config:cache
     * - Fallback getenv untuk external env sebagai safety net
     */
    private function timestampLeewaySeconds(): int
    {
        $v = config('tenrusl.signature.timestamp_leeway_seconds');

        if (is_numeric($v) && (int) $v > 0) {
            return (int) $v;
        }

        $env = getenv('TENRUSL_SIG_TS_LEEWAY_SECONDS');

        if (is_string($env) && ctype_digit($env) && (int) $env > 0) {
            return (int) $env;
        }

        // Default 300s (5 menit)
        return 300;
    }

    /**
     * Replay mitigation:
     * - Provider yang "timestamped signatures" (Stripe, Airwallex, Doku, PayPal)
     *   di strict mode: timestamp harus ada & bisa diparse.
     * - Jika timestamp valid: pastikan berada dalam window (now +/- leeway).
     */
    private function passesReplayMitigation(string $provider, Request $request, bool $strict): bool
    {
        $detail = $this->extractRequestTimestampDetail($provider, $request);

        // provider tidak punya timestamp policy
        if ($detail['state'] === 'not_applicable') {
            return true;
        }

        // strict: missing/unparseable => reject (ketat di production)
        if ($strict && ($detail['state'] === 'missing' || $detail['state'] === 'unparseable')) {
            Log::warning('Webhook timestamp missing/unparseable in strict mode', [
                'provider' => $provider,
                'request_id' => $this->requestId($request),
                'state' => $detail['state'],
            ]);

            return false;
        }

        // non-strict: missing/unparseable => allow (best-effort)
        if ($detail['state'] === 'missing' || $detail['state'] === 'unparseable') {
            return true;
        }

        $ts = $detail['timestamp'];
        if (! is_int($ts) || $ts <= 0) {
            return $strict ? false : true;
        }

        $now = time();
        $leeway = $this->timestampLeewaySeconds();

        $diff = abs($now - $ts);

        if ($diff <= $leeway) {
            return true;
        }

        // Outside tolerance -> reject
        // (Jika ini sering terjadi padahal signature valid, cek sinkronisasi waktu server/NTP).
        Log::warning('Webhook timestamp outside tolerance', [
            'provider' => $provider,
            'request_id' => $this->requestId($request),
            'now' => $now,
            'timestamp' => $ts,
            'diff_seconds' => $diff,
            'leeway_seconds' => $leeway,
        ]);

        return false;
    }

    /**
     * Extract request timestamp (epoch seconds) dari header/signature provider.
     *
     * Return detail:
     * - state: not_applicable | missing | unparseable | ok
     * - timestamp: int|null (epoch seconds)
     *
     * @return array{state: string, timestamp: int|null}
     */
    private function extractRequestTimestampDetail(string $provider, Request $request): array
    {
        // Stripe: Stripe-Signature: t=1492774577,v1=...
        if ($provider === 'stripe') {
            $hdr = $this->headerString($request, 'Stripe-Signature');
            if ($hdr === null) {
                return ['state' => 'missing', 'timestamp' => null];
            }

            if (preg_match('/(?:^|,)\s*t=(\d{9,13})\s*(?:,|$)/', $hdr, $m) === 1) {
                $n = (int) $m[1];

                // If ms, normalize to seconds
                if ($n > 20000000000) {
                    $n = (int) floor($n / 1000);
                }

                return $n > 0
                    ? ['state' => 'ok', 'timestamp' => $n]
                    : ['state' => 'unparseable', 'timestamp' => null];
            }

            return ['state' => 'unparseable', 'timestamp' => null];
        }

        // Airwallex: x-timestamp (epoch seconds OR ms)
        if ($provider === 'airwallex') {
            $hdr = $this->headerString($request, 'x-timestamp');
            if ($hdr === null) {
                return ['state' => 'missing', 'timestamp' => null];
            }

            if (! ctype_digit($hdr)) {
                return ['state' => 'unparseable', 'timestamp' => null];
            }

            $n = (int) $hdr;

            // Normalize ms -> seconds
            if ($n > 20000000000) {
                $n = (int) floor($n / 1000);
            }

            return $n > 0
                ? ['state' => 'ok', 'timestamp' => $n]
                : ['state' => 'unparseable', 'timestamp' => null];
        }

        // DOKU: Request-Timestamp (RFC3339)
        if ($provider === 'doku') {
            $hdr = $this->headerString($request, 'Request-Timestamp');
            if ($hdr === null) {
                return ['state' => 'missing', 'timestamp' => null];
            }

            $dt = $this->parseDateTimeToEpoch($hdr);

            return $dt !== null
                ? ['state' => 'ok', 'timestamp' => $dt]
                : ['state' => 'unparseable', 'timestamp' => null];
        }

        // PayPal: PAYPAL-TRANSMISSION-TIME (RFC3339)
        if ($provider === 'paypal') {
            $hdr = $this->headerString($request, 'PAYPAL-TRANSMISSION-TIME');
            if ($hdr === null) {
                return ['state' => 'missing', 'timestamp' => null];
            }

            $dt = $this->parseDateTimeToEpoch($hdr);

            return $dt !== null
                ? ['state' => 'ok', 'timestamp' => $dt]
                : ['state' => 'unparseable', 'timestamp' => null];
        }

        return ['state' => 'not_applicable', 'timestamp' => null];
    }

    private function parseDateTimeToEpoch(string $value): ?int
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        // Numeric epoch?
        if (ctype_digit($value)) {
            $n = (int) $value;

            if ($n > 20000000000) {
                $n = (int) floor($n / 1000);
            }

            return $n > 0 ? $n : null;
        }

        try {
            $dt = new \DateTimeImmutable($value);

            return $dt->getTimestamp();
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Extract signature-material presence + stable fingerprint hash (untuk audit).
     *
     * Return:
     * - present: apakah signature-material minimal terpenuhi (header/field ada)
     * - source : dari mana fingerprint diambil (mis. headers:stripe-signature)
     * - hash   : sha256 fingerprint (tanpa bocor value)
     *
     * @return array{present: bool, source: string|null, hash: string|null}
     */
    private function extractSignatureFingerprint(string $provider, Request $request, string $rawBody): array
    {
        // NOTE: urutan group penting (prefer signature header dibanding Authorization/token).
        $groups = match ($provider) {
            'stripe' => [
                ['Stripe-Signature'],
            ],
            'xendit' => [
                ['x-callback-token'],
            ],
            'tripay' => [
                ['X-Callback-Signature'],
            ],
            'lemonsqueezy' => [
                ['X-Signature'],
            ],
            'airwallex' => [
                ['x-timestamp', 'x-signature'],
            ],
            'doku' => [
                ['Client-Id', 'Request-Id', 'Request-Timestamp', 'Signature'],
            ],
            'dana' => [
                ['X-SIGNATURE'],
            ],
            'amazon_bwp' => [
                ['x-amzn-signature'],
            ],
            'paypal' => [
                ['PAYPAL-TRANSMISSION-ID', 'PAYPAL-TRANSMISSION-TIME', 'PAYPAL-TRANSMISSION-SIG', 'PAYPAL-CERT-URL', 'PAYPAL-AUTH-ALGO'],
            ],
            'payoneer' => [
                ['X-Payoneer-Signature'],
                ['Authorization'],
            ],
            'oy' => [
                ['X-OY-Signature'],
                ['X-Callback-Auth'],
                ['X-OY-Callback-Auth'],
                ['Authorization'],
            ],
            'mock' => [
                ['X-Mock-Signature'],
                ['Authorization'],
            ],
            'paddle' => [
                ['Paddle-Signature'],
                ['__body_field:p_signature'],
            ],
            'midtrans' => [
                ['__json_field:signature_key'],
            ],
            'skrill' => [
                ['__form_field:md5sig', '__form_field:sha2sig'],
            ],
            default => [
                ['Authorization'],
            ],
        };

        // A) Header-based groups
        foreach ($groups as $group) {
            $material = [];
            $sourceParts = [];

            $allPresent = true;

            foreach ($group as $key) {
                // Body-field sentinel
                if (str_starts_with($key, '__')) {
                    $allPresent = false;
                    break;
                }

                $val = $this->headerString($request, $key);
                if ($val === null) {
                    $allPresent = false;
                    break;
                }

                $kNorm = strtolower($key);
                $sourceParts[] = $kNorm;
                $material[] = $kNorm.':'.$val;
            }

            if ($allPresent) {
                $source = 'headers:'.implode(',', $sourceParts);
                $fingerprint = "provider:{$provider}\n".implode("\n", $material);

                return [
                    'present' => true,
                    'source' => $source,
                    'hash' => hash('sha256', $fingerprint),
                ];
            }
        }

        // B) Body-based checks (provider-specific)
        if ($provider === 'paddle') {
            $sig = $this->extractFormField($rawBody, 'p_signature');
            if ($sig !== null) {
                $fingerprint = "provider:{$provider}\nbody:p_signature={$sig}";

                return [
                    'present' => true,
                    'source' => 'body:p_signature',
                    'hash' => hash('sha256', $fingerprint),
                ];
            }

            return ['present' => false, 'source' => 'body:p_signature', 'hash' => null];
        }

        if ($provider === 'midtrans') {
            $sig = $this->extractJsonField($rawBody, 'signature_key');
            if ($sig !== null) {
                $fingerprint = "provider:{$provider}\njson:signature_key={$sig}";

                return [
                    'present' => true,
                    'source' => 'json:signature_key',
                    'hash' => hash('sha256', $fingerprint),
                ];
            }

            return ['present' => false, 'source' => 'json:signature_key', 'hash' => null];
        }

        if ($provider === 'skrill') {
            $md5 = $this->extractFormField($rawBody, 'md5sig');
            $sha2 = $this->extractFormField($rawBody, 'sha2sig');

            if ($md5 !== null || $sha2 !== null) {
                $fp = "provider:{$provider}\nform:md5sig=".($md5 ?? '')."\nform:sha2sig=".($sha2 ?? '');

                return [
                    'present' => true,
                    'source' => 'form:md5sig|sha2sig',
                    'hash' => hash('sha256', $fp),
                ];
            }

            return ['present' => false, 'source' => 'form:md5sig|sha2sig', 'hash' => null];
        }

        // Fallback: nothing recognized
        return ['present' => false, 'source' => null, 'hash' => null];
    }

    private function headerString(Request $request, string $key): ?string
    {
        $v = $request->headers->get($key);

        if (! is_string($v)) {
            return null;
        }

        $v = trim($v);

        return $v !== '' ? $v : null;
    }

    private function extractJsonField(string $rawBody, string $key): ?string
    {
        $decoded = json_decode($rawBody, true);
        if (! is_array($decoded)) {
            return null;
        }

        $v = $decoded[$key] ?? null;

        if ($v === null) {
            return null;
        }

        $s = trim((string) $v);

        return $s !== '' ? $s : null;
    }

    private function extractFormField(string $rawBody, string $key): ?string
    {
        $params = [];
        parse_str($rawBody, $params);

        if (! is_array($params)) {
            return null;
        }

        $v = $params[$key] ?? null;

        if (! is_string($v)) {
            return null;
        }

        $v = trim($v);

        return $v !== '' ? $v : null;
    }

    private function requestId(Request $request): string
    {
        $attr = $request->attributes->get('correlation_id');
        if (is_string($attr) && $attr !== '') {
            return $attr;
        }

        $hdr = $request->headers->get('X-Request-ID');
        if (is_string($hdr) && $hdr !== '') {
            return (string) $hdr;
        }

        return '';
    }

    /**
     * Audit log minimal, tanpa bocor secret.
     *
     * $result: ok|failed
     *
     * @param  array<string, mixed>  $extra
     */
    private function auditLog(string $result, Request $request, string $provider, array $extra = []): void
    {
        $base = [
            'provider' => $provider,
            'request_id' => $this->requestId($request),
            'result' => $result,
            'method' => (string) $request->getMethod(),
            'path' => (string) $request->path(),
            'ip' => (string) $request->ip(),
        ];

        $ctx = array_merge($base, $extra);

        if ($result === 'ok') {
            Log::info('Webhook signature verified', $ctx);

            return;
        }

        Log::warning('Webhook signature rejected', $ctx);
    }

    private function errorGeneric(int $status, string $code, string $message): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ], $status);
    }
}
