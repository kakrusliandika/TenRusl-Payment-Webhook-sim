<?php

// app/Http/Middleware/VerifyWebhookSignature.php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Signatures\SignatureVerifier;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * VerifyWebhookSignature
 * ----------------------
 * Middleware "gate" sebelum masuk domain:
 * - Mengambil {provider} dari route /api/v1/webhooks/{provider}
 * - Baca raw body
 * - Verifikasi signature via SignatureVerifier
 * - Kalau gagal: STOP dan kembalikan 401 JSON
 *
 * Catatan:
 * - Middleware ini idealnya dipasang hanya untuk route webhook (bukan global),
 *   karena bukan semua endpoint membutuhkan signature verification.
 * - Raw body disimpan ke request attribute 'tenrusl_raw_body' agar controller/
 *   FormRequest (WebhookRequest) bisa akses tanpa baca ulang stream.
 */
class VerifyWebhookSignature
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Provider dari parameter route: /webhooks/{provider}
        $provider = strtolower((string) $request->route('provider'));

        if ($provider === '') {
            return $this->unauthorized('Missing provider in route.');
        }

        // Safety tambahan: enforce allowlist di middleware juga
        $allowlist = (array) config('tenrusl.providers_allowlist', []);
        if ($allowlist !== [] && ! in_array($provider, $allowlist, true)) {
            return $this->unauthorized('Provider not allowed.', $provider);
        }

        // Ambil raw body. Ini yang dipakai untuk signature verification.
        $rawBody = (string) $request->getContent();

        if ($rawBody === '') {
            // Umumnya webhook selalu punya payload. Kalau kosong, anggap invalid.
            return $this->unauthorized('Empty webhook payload.', $provider);
        }

        // Simpan raw body agar bisa dipakai ulang (mis. WebhookRequest::rawBody()).
        $request->attributes->set('tenrusl_raw_body', $rawBody);

        try {
            // Delegasi ke service (source-of-truth)
            $verified = SignatureVerifier::verify($provider, $rawBody, $request);
        } catch (Throwable $e) {
            // Jangan bocorkan detail exception ke client.
            Log::warning('Webhook signature verification threw exception', [
                'provider' => $provider,
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return $this->unauthorized('Signature verification error.', $provider);
        }

        if (! $verified) {
            return $this->unauthorized('Invalid webhook signature.', $provider);
        }

        // Lolos verifikasi → lanjut ke controller / domain
        return $next($request);
    }

    /**
     * Helper respon 401 JSON yang konsisten.
     */
    private function unauthorized(string $message, ?string $provider = null): JsonResponse
    {
        $payload = ['message' => $message];

        if ($provider !== null && $provider !== '') {
            $payload['provider'] = $provider;
        }

        return response()->json($payload, 401);
    }
}
