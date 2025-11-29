<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Signatures\SignatureVerifier;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyWebhookSignature
{
    /**
     * Verifikasi signature webhook berdasarkan provider (route param: {provider}).
     *
     * Kontrak:
     * - Provider diambil dari parameter route: /webhooks/{provider}
     * - SignatureVerifier:
     *      - Membaca header spesifik provider (Stripe-Signature, X-Lemon-Signature, X-Tripay-Signature, dll)
     *      - Menggunakan raw body untuk HMAC (bukan hasil json_encode dari array request)
     *      - Menerapkan timestamp leeway di dalam service (baca dari config)
     *      - Membandingkan signature dengan constant-time compare (hash_equals)
     *
     * Jika verifikasi gagal, middleware mengembalikan 401 dan tidak meneruskan ke controller.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Provider diambil dari parameter route: /webhooks/{provider}
        $provider = (string) $request->route('provider');

        if ($provider === '') {
            return $this->unauthorized('Missing provider in route.');
        }

        // Raw body diperlukan untuk verifikasi HMAC yang benar
        $rawBody = (string) $request->getContent();

        if ($rawBody === '') {
            // Kebanyakan provider tidak mengirim webhook tanpa body
            return $this->unauthorized('Empty webhook payload.', $provider);
        }

        // Di sini kita hanya kirim provider + raw body + request.
        // Timestamp leeway & constant-time compare di-handle di SignatureVerifier.
        $verified = SignatureVerifier::verify($provider, $rawBody, $request);

        if (! $verified) {
            return $this->unauthorized('Invalid webhook signature.', $provider);
        }

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
