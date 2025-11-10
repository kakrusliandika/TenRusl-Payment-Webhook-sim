<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Signatures\SignatureVerifier;
use Closure;
use Illuminate\Http\Request;

class VerifyWebhookSignature
{
    /**
     * Verifikasi signature webhook berdasarkan provider (route param: {provider}).
     * Jika gagal, balas 401 tanpa memproses controller.
     */
    public function handle(Request $request, Closure $next)
    {
        // Provider diambil dari parameter route: /webhooks/{provider}
        $provider = (string) $request->route('provider');

        // Ambil raw body untuk verifikasi yang akurat
        $rawBody = (string) $request->getContent();

        if (!SignatureVerifier::verify($provider, $rawBody, $request)) {
            return response()->json([
                'message'   => 'Invalid webhook signature',
                'provider'  => $provider,
            ], 401);
        }

        return $next($request);
    }
}
