<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Signatures\SignatureVerifier;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * VerifyWebhookSignature
 * ----------------------
 * Gate sebelum domain logic:
 * - Ambil {provider} dari route /webhooks/{provider}
 * - Baca RAW body (bukan hasil decode)
 * - Simpan RAW body ke request attribute 'tenrusl_raw_body'
 * - Verifikasi signature via SignatureVerifier
 * - Kalau gagal: stop dan return 401 JSON
 */
class VerifyWebhookSignature
{
    public const RAW_BODY_ATTR = 'tenrusl_raw_body';

    /**
     * Optional DI: jika SignatureVerifier kamu dibuat sebagai service instance.
     * Kalau tidak ada, middleware akan fallback ke static SignatureVerifier::verify(...)
     */
    public function __construct(private readonly ?SignatureVerifier $verifier = null) {}

    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Safety: biarkan preflight lewat (biasanya OPTIONS route tidak dipasang middleware ini)
        if ($request->isMethod('OPTIONS')) {
            return response()->noContent(Response::HTTP_NO_CONTENT);
        }

        $provider = strtolower((string) $request->route('provider'));
        if ($provider === '') {
            return $this->unauthorized('Missing provider in route.');
        }

        // Enforce allowlist juga di middleware (defense-in-depth)
        $allowlist = (array) config('tenrusl.providers_allowlist', []);
        if ($allowlist !== []) {
            $allowlist = array_values(array_unique(array_map(
                static fn ($p) => strtolower((string) $p),
                $allowlist
            )));

            if (! in_array($provider, $allowlist, true)) {
                return $this->unauthorized('Provider not allowed.', $provider);
            }
        }

        // Ambil RAW body (source-of-truth untuk signature)
        // getContent() aman dipanggil dan nilainya dicache oleh Request.
        $rawBody = (string) $request->getContent();

        // Tetap simpan rawBody ke attribute agar downstream (controller/FormRequest) bisa pakai
        $request->attributes->set(self::RAW_BODY_ATTR, $rawBody);

        if (trim($rawBody) === '') {
            // Webhook biasanya selalu membawa payload; kalau kosong anggap invalid
            return $this->unauthorized('Empty webhook payload.', $provider);
        }

        try {
            $verified = $this->verifySignature($provider, $rawBody, $request);
        } catch (Throwable $e) {
            // Jangan bocorkan detail error/verifier ke client
            Log::warning('Webhook signature verification exception', [
                'provider' => $provider,
                'message' => $e->getMessage(),
            ]);

            return $this->unauthorized('Signature verification error.', $provider);
        }

        if (! $verified) {
            return $this->unauthorized('Invalid webhook signature.', $provider);
        }

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

        throw new RuntimeException('SignatureVerifier verify method is not available.');
    }

    /**
     * Helper respon 401 JSON yang konsisten.
     */
    private function unauthorized(string $message, ?string $provider = null): JsonResponse
    {
        $payload = [
            'error' => [
                'code' => 'unauthorized',
                'message' => $message,
            ],
        ];

        if ($provider !== null && $provider !== '') {
            $payload['error']['provider'] = $provider;
        }

        return response()->json($payload, Response::HTTP_UNAUTHORIZED);
    }
}
