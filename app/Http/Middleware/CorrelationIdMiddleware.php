<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * CorrelationIdMiddleware
 * -----------------------
 * Menjaga request id konsisten untuk tracing:
 * - Ambil request id dari upstream jika ada (mis. LB / gateway / API management):
 *     - X-Request-ID (utama, akan dipropagate)
 *     - X-Correlation-ID
 *     - traceparent (W3C Trace Context) -> pakai trace-id sebagai correlation id
 * - Jika tidak ada / invalid -> generate ULID
 * - Simpan ke request attribute
 * - Inject ke Context (agar ikut ke logging metadata + terbawa ke queued job)
 * - Propagate ke response header X-Request-ID
 *
 * Catatan:
 * - Agar FE bisa membaca header ini di browser (cross-origin),
 *   CORS harus expose header lewat Access-Control-Expose-Headers.
 */
class CorrelationIdMiddleware
{
    /** Header canonical yang kita gunakan keluar-masuk. */
    public const HEADER = 'X-Request-ID';

    /** Attribute name untuk dipakai downstream (controller/job/log). */
    public const ATTR = 'correlation_id';

    /** Attribute tambahan untuk audit/debug. */
    public const ATTR_SOURCE = 'correlation_id_source';

    /**
     * Upstream header candidates (urut prioritas).
     * Catatan: header di HTTP case-insensitive, tapi kita tulis canonical.
     *
     * Kamu bisa override via config('tenrusl.correlation_id.upstream_headers')
     * untuk menyesuaikan dengan infra kamu.
     *
     * @var string[]
     */
    private const DEFAULT_UPSTREAM_HEADERS = [
        'X-Request-ID',
        'X-Correlation-ID',
        'X-Correlation-Id',
        'X-Request-Id',
        'Traceparent',
        'traceparent',
    ];

    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        [$requestId, $source] = $this->resolveRequestId($request);

        // Simpan di attribute + header request (biar downstream konsisten)
        $request->attributes->set(self::ATTR, $requestId);
        $request->attributes->set(self::ATTR_SOURCE, $source);
        $request->headers->set(self::HEADER, $requestId);

        // Context (utama): otomatis ikut ke metadata log & ikut kebawa ke queued jobs.
        $this->addToContext($requestId, $source);

        try {
            /** @var \Symfony\Component\HttpFoundation\Response $response */
            $response = $next($request);
        } catch (Throwable $e) {
            // Pastikan error response juga membawa X-Request-ID
            $response = $this->renderException($request, $e);
        }

        $response->headers->set(self::HEADER, $requestId);

        return $response;
    }

    /**
     * Tentukan request id terbaik:
     * - ambil dari upstream jika valid,
     * - else generate ULID.
     *
     * @return array{0:string,1:string} [requestId, source]
     */
    private function resolveRequestId(Request $request): array
    {
        $headers = config('tenrusl.correlation_id.upstream_headers');
        if (! is_array($headers) || $headers === []) {
            $headers = self::DEFAULT_UPSTREAM_HEADERS;
        }

        foreach ($headers as $h) {
            if (! is_string($h) || trim($h) === '') {
                continue;
            }

            $raw = (string) ($request->headers->get($h) ?? '');
            $raw = trim($raw);
            if ($raw === '') {
                continue;
            }

            // Special-case: traceparent -> ambil trace-id sebagai correlation id
            if (strtolower($h) === 'traceparent') {
                $traceId = $this->parseTraceparentTraceId($raw);
                if ($traceId !== null) {
                    return [$traceId, 'traceparent'];
                }

                continue;
            }

            $id = $this->sanitizeRequestId($raw);
            if ($id !== '') {
                return [$id, $h];
            }
        }

        // Fallback: generate ULID
        return [strtolower((string) Str::ulid()), 'generated'];
    }

    /**
     * Tambahkan request id ke Context (ideal) atau fallback ke Log context.
     *
     * - Context: akan otomatis ditambahkan sebagai metadata di log
     *   dan ikut terbawa ke job queue.
     * - Fallback Log context dipakai hanya jika Context tidak tersedia.
     */
    private function addToContext(string $requestId, string $source): void
    {
        $data = [
            'request_id' => $requestId,
            'request_id_source' => $source,
        ];

        // Laravel Context (recommended)
        try {
            if (class_exists(Context::class)) {
                Context::add($data);

                return;
            }
        } catch (Throwable) {
            // lanjut fallback
        }

        // Fallback: inject ke Log context (untuk Laravel lama)
        try {
            if (method_exists(Log::class, 'shareContext')) {
                Log::shareContext($data);

                return;
            }
        } catch (Throwable) {
            // lanjut fallback
        }

        try {
            Log::withContext($data);
        } catch (Throwable) {
            // ignore
        }
    }

    /**
     * Render exception menggunakan ExceptionHandler Laravel, lalu return response-nya.
     * Ini menjaga behavior default Laravel, tapi memastikan header X-Request-ID hadir.
     */
    private function renderException(Request $request, Throwable $e): Response
    {
        try {
            /** @var \Illuminate\Contracts\Debug\ExceptionHandler $handler */
            $handler = app(ExceptionHandler::class);

            $response = $handler->render($request, $e);
            if ($response instanceof Response) {
                return $response;
            }
        } catch (Throwable) {
            // fallback
        }

        // Fallback terakhir: response 500 generik
        return response('Internal Server Error', 500);
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

    /**
     * Parse W3C traceparent: "version-traceid-parentid-flags"
     * Ambil trace-id (32 hex) sebagai correlation id (lebih stabil lintas sistem).
     */
    private function parseTraceparentTraceId(string $traceparent): ?string
    {
        $traceparent = trim($traceparent);

        // contoh: 00-4bf92f3577b34da6a3ce929d0e0e4736-00f067aa0ba902b7-01
        if (preg_match('/^[\da-f]{2}-([\da-f]{32})-[\da-f]{16}-[\da-f]{2}$/i', $traceparent, $m) === 1) {
            return strtolower($m[1]);
        }

        return null;
    }
}
