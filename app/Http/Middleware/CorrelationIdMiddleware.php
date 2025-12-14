<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * CorrelationIdMiddleware
 * -----------------------
 * Menjaga X-Request-ID konsisten untuk tracing:
 * - Pakai X-Request-ID dari client jika valid
 * - Jika tidak ada -> generate ULID
 * - Simpan ke request attribute
 * - Inject ke Laravel Context (jika tersedia)
 * - Inject ke Log context (shareContext / withContext)
 * - Propagate ke response header X-Request-ID
 *
 * Catatan:
 * - Agar FE bisa membaca header ini di browser (cross-origin),
 *   CORS harus expose header lewat Access-Control-Expose-Headers. :contentReference[oaicite:1]{index=1}
 * - Contoh pola middleware + Log::withContext + set response header ada di docs Laravel. :contentReference[oaicite:2]{index=2}
 * - Context::add juga direkomendasikan via middleware untuk trace metadata lintas request/job. :contentReference[oaicite:3]{index=3}
 */
class CorrelationIdMiddleware
{
    public const HEADER = 'X-Request-ID';
    public const ATTR = 'correlation_id';

    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $incoming = (string) ($request->headers->get(self::HEADER) ?? '');
        $requestId = $this->sanitizeRequestId($incoming);

        if ($requestId === '') {
            $requestId = strtolower((string) Str::ulid());
        }

        // Simpan di attribute + header request (biar downstream konsisten)
        $request->attributes->set(self::ATTR, $requestId);
        $request->headers->set(self::HEADER, $requestId);

        // 1) Laravel Context (kalau tersedia)
        $this->addToLaravelContext($request, $requestId);

        // 2) Log context (shareContext jika ada, fallback withContext)
        $this->addToLogContext($requestId);

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);

        // Pastikan header selalu ada di response (payments + admin + webhooks)
        $response->headers->set(self::HEADER, $requestId);

        return $response;
    }

    private function addToLaravelContext(Request $request, string $requestId): void
    {
        try {
            // Guard agar tidak crash jika facade Context tidak tersedia di versi tertentu
            if (class_exists(\Illuminate\Support\Facades\Context::class)) {
                \Illuminate\Support\Facades\Context::add([
                    'request_id' => $requestId,
                    'url' => $request->url(),
                ]);
            }
        } catch (Throwable $e) {
            // jangan bikin request gagal hanya karena context
        }
    }

    private function addToLogContext(string $requestId): void
    {
        try {
            if (method_exists(Log::class, 'shareContext')) {
                Log::shareContext(['request_id' => $requestId]);
                return;
            }
        } catch (Throwable $e) {
            // lanjut fallback
        }

        try {
            Log::withContext(['request_id' => $requestId]);
        } catch (Throwable $e) {
            // ignore
        }
    }

    /**
     * Sanitasi request id:
     * - trim
     * - batasi panjang
     * - whitelist karakter aman (alnum + - _ . :)
     */
    private function sanitizeRequestId(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            return '';
        }

        if (strlen($value) > 128) {
            $value = substr($value, 0, 128);
        }

        $value = preg_replace('/[^A-Za-z0-9\-\_\.\:]/', '', $value) ?? '';

        return $value;
    }
}
