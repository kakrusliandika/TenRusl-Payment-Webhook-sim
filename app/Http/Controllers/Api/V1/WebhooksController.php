<?php
// app/Http/Controllers/Api/V1/WebhooksController.php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\WebhookRequest;
use App\Services\Webhooks\WebhookProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * WebhooksController (API v1)
 * --------------------------
 * Endpoint:
 * - POST /api/v1/webhooks/{provider}
 *
 * Penting:
 * - receive() WAJIB type-hint WebhookRequest supaya validasi benar-benar dipakai.
 * - Signature verification SUDAH dicegat oleh middleware 'verify.webhook.signature'
 *   di routes/api.php (jadi yang masuk ke sini harus sudah lolos signature).
 */
class WebhooksController extends Controller
{
    public function __construct(
        private readonly WebhookProcessor $processor
    ) {}

    /**
     * POST /api/v1/webhooks/{provider}
     */
    public function receive(WebhookRequest $request, string $provider): JsonResponse
    {
        // Ambil raw body:
        // - Ideal: WebhookRequest punya method rawBody() yang ambil dari attribute 'tenrusl_raw_body'
        // - Fallback: Request::getContent()
        $rawBody = method_exists($request, 'rawBody')
            ? (string) $request->rawBody()
            : (string) $request->getContent();

        // Deteksi content-type
        $contentType = $this->detectContentType($request);

        // Parse payload jadi array (JSON atau form-urlencoded)
        $payload = $this->parsePayload($rawBody, $contentType);

        // Data tervalidasi dari FormRequest (kalau ada field resmi seperti event_id/type)
        $validated = $request->validated();

        // event_id priority:
        // 1) dari input tervalidasi (kalau disediakan)
        // 2) dari payload (extract)
        // 3) generate fallback
        $eventId = $validated['event_id']
            ?? $this->extractEventId($payload)
            ?? ('evt_' . (string) Str::ulid());

        // type priority:
        // 1) dari input tervalidasi
        // 2) dari payload
        // 3) default string kosong (processor boleh handle)
        $type = (string) (
            $validated['type']
            ?? $this->extractType($payload)
            ?? ''
        );

        // Proses domain core (dedup + update payment + retry scheduling)
        $result = $this->processor->process(
            $provider,
            (string) $eventId,
            $type,
            $rawBody,
            $payload
        );

        // 202 Accepted: diterima dan diproses secara idempotent/retry-aware
        return response()->json([
            'data' => [
                'event' => [
                    'provider' => $provider,
                    'event_id' => (string) $eventId,
                    'type' => $type !== '' ? $type : null,
                ],
                'result' => $result,
            ],
        ], 202);
    }

    /**
     * Ambil Content-Type dari Request secara defensif.
     */
    private function detectContentType(WebhookRequest $request): string
    {
        $header = $request->header('Content-Type');

        if (is_string($header) && $header !== '') {
            return $header;
        }

        // Fallback untuk kasus proxy/server tertentu
        return (string) (
            $request->server('CONTENT_TYPE')
            ?? $request->server('HTTP_CONTENT_TYPE')
            ?? ''
        );
    }

    /**
     * Parse payload sesuai content-type:
     * - application/json
     * - application/x-www-form-urlencoded
     * Selain itu: return [] (payload tidak dikenali / tidak diparsing).
     */
    private function parsePayload(string $rawBody, ?string $contentType): array
    {
        $ct = strtolower((string) $contentType);

        if (str_contains($ct, 'application/json')) {
            $arr = json_decode($rawBody, true);

            return is_array($arr) ? $arr : [];
        }

        if (str_contains($ct, 'application/x-www-form-urlencoded')) {
            $out = [];
            parse_str($rawBody, $out);

            return is_array($out) ? $out : [];
        }

        return [];
    }

    /**
     * Ekstrak event id dari payload jika provider mengirim id di field tertentu.
     * (Ini fallback, bukan satu-satunya sumber kebenaran).
     */
    private function extractEventId(array $p): ?string
    {
        foreach ([
            Arr::get($p, 'id'),
            Arr::get($p, 'event_id'),
            Arr::get($p, 'data.id'),
            Arr::get($p, 'resource.id'),
            Arr::get($p, 'object.id'),
            Arr::get($p, 'data.object.id'),
        ] as $v) {
            if (is_string($v) && $v !== '') {
                return $v;
            }
        }

        return null;
    }

    /**
     * Ekstrak type/event name dari payload (fallback).
     */
    private function extractType(array $p): ?string
    {
        foreach ([
            Arr::get($p, 'type'),
            Arr::get($p, 'event'),
            Arr::get($p, 'event_type'),
            Arr::get($p, 'data.type'),
        ] as $v) {
            if (is_string($v) && $v !== '') {
                return $v;
            }
        }

        return null;
    }
}
