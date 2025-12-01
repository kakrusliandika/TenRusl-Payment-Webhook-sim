<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CorrelationIdMiddleware
{
    public const HEADER = 'X-Request-ID';
    public const ATTR = 'correlation_id';

    /**
     * Tambah / jaga Request ID di setiap request & response, dan injeksikan ke log context.
     *
     * @param  \Closure(\Illuminate\Http\Request): \Symfony\Component\HttpFoundation\Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $incoming = (string) ($request->headers->get(self::HEADER) ?? '');
        $requestId = $this->sanitizeRequestId($incoming);

        if ($requestId === '') {
            $requestId = strtolower((string) Str::ulid());
        }

        // Set ke request attribute agar bisa diakses controller/service
        $request->attributes->set(self::ATTR, $requestId);

        // (Optional) Set juga ke request header supaya downstream yang baca header tetap dapat nilainya
        $request->headers->set(self::HEADER, $requestId);

        // Share ke logging context:
        // - Laravel baru: Log::shareContext()
        // - Laravel lama: Log::withContext()
        // shareContext dibahas di dokumentasi logging. :contentReference[oaicite:1]{index=1}
        if (method_exists(Log::class, 'shareContext')) {
            Log::shareContext(['request_id' => $requestId]);
        } else {
            Log::withContext(['request_id' => $requestId]); // ada sejak Laravel 8.49+ :contentReference[oaicite:2]{index=2}
        }

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);

        // Propagate ke response header agar client bisa trace.
        $response->headers->set(self::HEADER, $requestId);

        return $response;
    }

    /**
     * Sanitasi request id agar aman (hindari header injection / terlalu panjang).
     */
    private function sanitizeRequestId(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            return '';
        }

        // Max length proteksi
        if (strlen($value) > 128) {
            $value = substr($value, 0, 128);
        }

        // Izinkan: alnum + - _ . :
        $value = preg_replace('/[^A-Za-z0-9\-\_\.\:]/', '', $value) ?? '';

        return $value;
    }
}
