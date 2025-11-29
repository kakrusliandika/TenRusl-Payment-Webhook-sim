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
        // Ambil dari header kalau ada, kalau tidak generate ULID (lowercase).
        $incoming = (string) ($request->headers->get(self::HEADER) ?? '');
        $requestId = $incoming !== '' ? $incoming : strtolower((string) Str::ulid());

        // Simpan ke attribute supaya bisa diakses handler / service.
        $request->attributes->set(self::ATTR, $requestId);

        // Share ke log context, sehingga semua log di request ini punya field "request_id".
        // Di Laravel 10+ kita bisa pakai shareContext (di bawah ini tetap kompatibel).
        if (method_exists(Log::getLogger(), 'shareContext')) {
            // Laravel 11/12 style (Monolog v3)
            Log::shareContext(['request_id' => $requestId]);
        } else {
            // Fallback: gunakan withContext per log call (beberapa logger/versi lama)
            Log::withContext(['request_id' => $requestId]);
        }

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);

        // Propagate ke response header agar client bisa trace.
        $response->headers->set(self::HEADER, $requestId);

        return $response;
    }
}
