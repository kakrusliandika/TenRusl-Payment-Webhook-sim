<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CorrelationIdMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $header = 'X-Request-Id';
        $id = $request->headers->get($header) ?: (string) Str::uuid();

        // simpan di attributes agar bisa dipakai di log/response builder
        $request->attributes->set('request_id', $id);

        $response = $next($request);
        $response->headers->set($header, $id);

        return $response;
    }
}
