<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);

        // === Strict-Transport-Security (aktif hanya bila HTTPS & bukan lokal) ===
        // HSTS melindungi dari downgrade attack. Terapkan di prod setelah site full HTTPS.
        // preload opsional; includeSubDomains disarankan jika semua subdomain sudah HTTPS.
        $isHttps = $request->isSecure() || $request->headers->get('X-Forwarded-Proto') === 'https';
        if ($isHttps && !app()->environment('local')) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // === MIME sniffing protection ===
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // === Clickjacking protection (pakai CSP frame-ancestors kalau sudah ada CSP) ===
        // SAMEORIGIN = boleh di-embed oleh origin sendiri, aman untuk dashboard.
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // === Referrer privacy ===
        // strict-origin-when-cross-origin = default modern yang aman & praktis.
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // === Tambahan aman & tidak mengganggu ===
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $response->headers->set('X-Download-Options', 'noopen'); // IE/Edge lama
        $response->headers->set('X-DNS-Prefetch-Control', 'off'); // cegah DNS prefetch liar

        // Catatan CSP:
        // Header CSP sebaiknya dikelola via spatie/laravel-csp (lihat bagian B).
        return $response;
    }
}
