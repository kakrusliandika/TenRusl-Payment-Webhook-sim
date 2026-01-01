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
 * - Kalau gagal: stop dan return error JSON.
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
    public function __construct(private readonly ?SignatureVerifier $verifier = null)
    {
    }

    /**
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, \Closure $next): Response
    {
        // Preflight (biasanya OPTIONS route tidak dipasang middleware ini)
        if ($request->isMethod('OPTIONS')) {
            return response()->noContent(Response::HTTP_NO_CONTENT);
        }

        if (!$request->isMethod('POST')) {
            return $this->error(
                status: Response::HTTP_METHOD_NOT_ALLOWED,
                code: 'method_not_allowed',
                message: 'Method not allowed.'
            );
        }

        $provider = strtolower((string) $request->route('provider'));
        if ($provider === '') {
            return $this->error(
                status: Response::HTTP_UNAUTHORIZED,
                code: 'unauthorized',
                message: 'Missing provider in route.'
            );
        }

        // Enforce allowlist (deny-by-default jika list kosong)
        $allowlist = $this->normalizeAllowlist((array) config('tenrusl.providers_allowlist', []));
        if ($allowlist === [] || !in_array($provider, $allowlist, true)) {
            $this->auditLog('failed', $request, $provider, [
                'reason' => 'provider_not_allowed',
            ]);

            return $this->error(
                status: Response::HTTP_UNAUTHORIZED,
                code: 'unauthorized',
                message: 'Provider not allowed.',
                provider: $provider
            );
        }

        // Enforce Content-Type
        $contentType = $this->normalizeContentType($request);
        if ($contentType === '' || !$this->isAllowedContentType($provider, $contentType)) {
            $this->auditLog('failed', $request, $provider, [
                'reason' => 'unsupported_content_type',
                'content_type' => $contentType,
            ]);

            return $this->error(
                status: Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                code: 'unsupported_media_type',
                message: 'Unsupported Content-Type.',
                provider: $provider
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

                return $this->error(
                    status: Response::HTTP_REQUEST_ENTITY_TOO_LARGE,
                    code: 'payload_too_large',
                    message: 'Payload too large.',
                    provider: $provider
                );
            }
        }

        // Ambil RAW body (source-of-truth untuk signature)
        // getContent() aman dipanggil dan nilainya dicache oleh Request.
        $rawBody = (string) $request->getContent();
        $rawBytes = strlen($rawBody);

        // Simpan raw body untuk downstream (controller/FormRequest)
        $request->attributes->set(self::RAW_BODY_ATTR, $rawBody);

        if ($rawBytes === 0) {
            $this->auditLog('failed', $request, $provider, [
                'reason' => 'empty_payload',
            ]);

            return $this->error(
                status: Response::HTTP_BAD_REQUEST,
                code: 'invalid_payload',
                message: 'Empty webhook payload.',
                provider: $provider
            );
        }

        if ($rawBytes > $maxBytes) {
            $this->auditLog('failed', $request, $provider, [
                'reason' => 'payload_too_large',
                'payload_bytes' => $rawBytes,
                'max_bytes' => $maxBytes,
            ]);

            return $this->error(
                status: Response::HTTP_REQUEST_ENTITY_TOO_LARGE,
                code: 'payload_too_large',
                message: 'Payload too large.',
                provider: $provider
            );
        }

        // Validasi signature-material wajib (header / body field) per provider
        $sig = $this->extractSignatureFingerprint($provider, $request, $rawBody);

        // Simpan fingerprint untuk audit/persist (tanpa bocor secret)
        if ($sig['hash'] !== null) {
            $request->attributes->set(self::SIG_HASH_ATTR, $sig['hash']);
        }
        if ($sig['source'] !== null) {
            $request->attributes->set(self::SIG_SOURCE_ATTR, $sig['source']);
        }

        if (!$sig['present']) {
            $this->auditLog('failed', $request, $provider, [
                'reason' => 'missing_signature_material',
                'signature_source' => $sig['source'],
                'signature_hash' => $sig['hash'],
            ]);

            return $this->error(
                status: Response::HTTP_UNAUTHORIZED,
                code: 'unauthorized',
                message: 'Missing signature.',
                provider: $provider
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

            return $this->error(
                status: Response::HTTP_UNAUTHORIZED,
                code: 'unauthorized',
                message: 'Signature verification error.',
                provider: $provider
            );
        }

        if (!$verified) {
            $this->auditLog('failed', $request, $provider, [
                'reason' => 'invalid_signature',
                'signature_source' => $sig['source'],
                'signature_hash' => $sig['hash'],
            ]);

            return $this->error(
                status: Response::HTTP_UNAUTHORIZED,
                code: 'unauthorized',
                message: 'Invalid webhook signature.',
                provider: $provider
            );
        }

        $request->attributes->set(self::SIG_VERIFIED_ATTR, true);

        $this->auditLog('ok', $request, $provider, [
            'signature_source' => $sig['source'],
            'signature_hash' => $sig['hash'],
        ]);

        return $next($request);
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
     * @param array<int, mixed> $allow
     *
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

        if (!is_string($ct) || trim($ct) === '') {
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
     * - Fallback ke env untuk backward compatibility
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
                // Billing HMAC header
                ['Paddle-Signature'],
                // Classic RSA signature in form body
                ['__body_field:p_signature'],
            ],
            'midtrans' => [
                ['__json_field:signature_key'],
            ],
            'skrill' => [
                ['__form_field:md5sig', '__form_field:sha2sig'],
            ],
            default => [
                // Default: minimal 1 of these should exist
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

        if (!is_string($v)) {
            return null;
        }

        $v = trim($v);

        return $v !== '' ? $v : null;
    }

    private function extractJsonField(string $rawBody, string $key): ?string
    {
        $decoded = json_decode($rawBody, true);
        if (!is_array($decoded)) {
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

        if (!is_array($params)) {
            return null;
        }

        $v = $params[$key] ?? null;

        if (!is_string($v)) {
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
     * @param array<string, mixed> $extra
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

    private function error(int $status, string $code, string $message, ?string $provider = null): JsonResponse
    {
        $payload = [
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ];

        if ($provider !== null && $provider !== '') {
            $payload['error']['provider'] = $provider;
        }

        return response()->json($payload, $status);
    }
}
