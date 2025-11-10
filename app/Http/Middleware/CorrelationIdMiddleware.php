<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CorrelationIdMiddleware
{
    public const HEADER = 'X-Correlation-Id';
    public const ATTR   = 'correlation_id';

    /**
     * Tambah / jaga Correlation-Id di setiap request & response.
     */
    public function handle(Request $request, Closure $next)
    {
        $incoming = (string) ($request->headers->get(self::HEADER) ?? '');
        $cid      = $incoming !== '' ? $incoming : strtolower((string) Str::ulid());

        // simpan ke attribute agar bisa diakses handler / logger
        $request->attributes->set(self::ATTR, $cid);

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);

        // propagate ke response
        $response->headers->set(self::HEADER, $cid);

        return $response;
    }
}
