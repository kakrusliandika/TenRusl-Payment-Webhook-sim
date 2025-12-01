<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Signatures\SignatureVerifier;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class VerifyWebhookSignature
{
    /**
     * Verifikasi signature webhook berdasarkan provider (route param: {provider}).
     *
     * Kontrak:
     * - Provider diambil dari parameter route: /webhooks/{provider}
     * - SignatureVerifier:
     *      - Membaca header spesifik provider (Stripe-Signature, X-*, dll)
     *      - Menggunakan raw body untuk HMAC (bukan hasil json_encode dari array request)
     *      - Menerapkan timestamp leeway di dalam service (baca dari config)
     *      - Membandingkan signature dengan constant-time compare (hash_equals)
     *
     * Jika verifikasi gagal, middleware mengembalikan 401 JSON
     * dan tidak meneruskan ke controller.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Provider diambil dari parameter route: /webhooks/{provider}
        $provider = strtolower((string) $request->route('provider'));

        if ($provider === '') {
            return $this->unauthorized('Missing provider in route.');
        }

        // Optional safety: hanya izinkan provider terdaftar
        $allowlist = (array) config('tenrusl.providers_allowlist', []);
        if ($allowlist !== [] && ! in_array($provider, $allowlist, true)) {
            return $this->unauthorized('Provider not allowed.', $provider);
        }

        // Baca raw body (Laravel/Symfony cache content, jadi aman dipakai ulang)
        $rawBody = (string) $request->getContent();

        if ($rawBody === '') {
            // Kebanyakan provider tidak mengirim webhook tanpa body
            return $this->unauthorized('Empty webhook payload.', $provider);
        }

        // Simpan raw body ke attribute request supaya bisa dipakai ulang
        // oleh WebhookRequest::rawBody() dan layer lain tanpa baca stream ulang.
        $request->attributes->set('tenrusl_raw_body', $rawBody);

        try {
            // Verifikasi signature via service (source of truth)
            $verified = SignatureVerifier::verify($provider, $rawBody, $request);
        } catch (Throwable $e) {
            // Kalau verifier melempar exception, jangan expose detail ke client.
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

        // Lolos verifikasi → lanjut ke controller
        return $next($request);
    }

    /**
     * Helper untuk respons 401 JSON yang konsisten.
     */
    private function unauthorized(string $message, ?string $provider = null): JsonResponse
    {
        $payload = [
            'message' => $message,
        ];

        if ($provider !== null && $provider !== '') {
            $payload['provider'] = $provider;
        }

        return response()->json($payload, 401);
    }
}
