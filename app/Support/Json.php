<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Json
{
    /**
     * Respons sukses seragam.
     */
    public static function ok(
        array|Arrayable|null $data = null,
        int $status = 200,
        ?string $requestId = null,
        array $meta = []
    ): JsonResponse {
        $payload = [
            'success' => true,
            'data'    => $data instanceof Arrayable ? $data->toArray() : ($data ?? (object) []),
            'meta'    => $meta,
        ];

        $resp = response()->json($payload, $status);
        if ($requestId) {
            $resp->headers->set('X-Request-Id', $requestId);
        }

        return $resp;
    }

    /**
     * Respons error seragam.
     */
    public static function error(
        string $message,
        string $code = 'error',
        int $httpStatus = 400,
        ?string $requestId = null,
        array $extra = []
    ): JsonResponse {
        $payload = array_merge([
            'success' => false,
            'error'   => $code,
            'message' => $message,
        ], $extra);

        $resp = response()->json($payload, $httpStatus);
        if ($requestId) {
            $resp->headers->set('X-Request-Id', $requestId);
        }

        return $resp;
    }

    /**
     * Ambil X-Request-Id dari Request attributes (di-set oleh CorrelationIdMiddleware).
     */
    public static function requestIdFrom(Request $request): ?string
    {
        $id = $request->attributes->get('request_id');
        return is_string($id) ? $id : null;
    }
}
