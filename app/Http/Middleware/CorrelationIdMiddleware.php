<?php

// app/Http/Middleware/CorrelationIdMiddleware.php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * CorrelationIdMiddleware
 * -----------------------
 * Menjaga "X-Request-ID" konsisten di request & response untuk tracing/logging:
 *
 * - Jika client kirim X-Request-ID => pakai (setelah disanitasi)
 * - Kalau tidak ada => generate ULID
 * - Simpan ke request attribute (agar controller/service mudah akses)
 * - Inject ke Laravel Context (biar kebawa ke logs + bisa “nyambung” ke job)
 * - Tambah juga ke log context (fallback / kompatibilitas)
 * - Propagate ke response header X-Request-ID
 *
 * Idealnya middleware ini dipasang global supaya semua endpoint (payments, webhooks, dll)
 * punya trace id yang sama, sehingga log mudah ditelusuri.
 */
class CorrelationIdMiddleware
{
    public const HEADER = 'X-Request-ID';

    /**
     * Attribute key di request:
     * $request->attributes->get(self::ATTR)
     */
    public const ATTR = 'correlation_id';

    /**
     * @param  \Closure(\Illuminate\Http\Request): \Symfony\Component\HttpFoundation\Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil dari header (opsional), lalu sanitasi untuk keamanan.
        $incoming = (string) ($request->headers->get(self::HEADER) ?? '');
        $requestId = $this->sanitizeRequestId($incoming);

        // Kalau kosong, generate ULID (lowercase) biar rapih di log.
        if ($requestId === '') {
            $requestId = strtolower((string) Str::ulid());
        }

        // Simpan ke request attribute agar bisa dipakai di controller/service.
        $request->attributes->set(self::ATTR, $requestId);

        // Hard-set ke request header juga (kadang downstream baca dari header).
        $request->headers->set(self::HEADER, $requestId);

        /**
         * 1) Laravel Context (recommended):
         *    Ini yang “ideal” untuk trace nyambung lintas request/job/command.
         */
        try {
            // Context facade tersedia di Laravel modern.
            Context::add('request_id', $requestId);
        } catch (Throwable $e) {
            // Kalau ternyata Context tidak tersedia, abaikan dan pakai log context saja.
        }

        /**
         * 2) Log context (kompatibilitas / tambahan):
         *    - shareContext: bila tersedia, share ke semua channel logger
         *    - withContext : fallback yang umum
         */
        try {
            // Kalau method ada, ini akan “shared” di logger manager (Laravel tertentu).
            Log::shareContext(['request_id' => $requestId]);
        } catch (Throwable $e) {
            // Fallback yang paling umum & stabil.
            try {
                Log::withContext(['request_id' => $requestId]);
            } catch (Throwable $e2) {
                // Kalau versi Laravel sangat tua, minimal jangan bikin request gagal.
            }
        }

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);

        // Propagate ke response header agar client bisa trace request ini.
        $response->headers->set(self::HEADER, $requestId);

        return $response;
    }

    /**
     * Sanitasi request id agar aman:
     * - trim
     * - batasi panjang (hindari header injection + payload besar)
     * - whitelist karakter (alnum + - _ . :)
     */
    private function sanitizeRequestId(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            return '';
        }

        // Batas panjang proteksi
        if (strlen($value) > 128) {
            $value = substr($value, 0, 128);
        }

        // Izinkan karakter aman saja
        $value = preg_replace('/[^A-Za-z0-9\-\_\.\:]/', '', $value) ?? '';

        return $value;
    }
}
