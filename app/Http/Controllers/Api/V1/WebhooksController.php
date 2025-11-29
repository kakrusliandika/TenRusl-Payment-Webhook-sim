<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\WebhookRequest;
use App\Services\Webhooks\WebhookProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class WebhooksController extends Controller
{
    public function __construct(
        private readonly WebhookProcessor $processor
    ) {}

    /** POST /api/v1/webhooks/{provider} */
    public function receive(WebhookRequest $request, string $provider): JsonResponse
    {
        $rawBody = $request->rawBody();
        $contentType = $this->detectContentType($request);
        $payload = $this->parsePayload($rawBody, $contentType);

        // Ambil input tervalidasi (event_id/type jika dikirim sebagai field resmi)
        $validated = $request->validated();

        // event_id:
        // 1. pakai dari request (jika lolos validasi),
        // 2. fallback ke hasil ekstraksi dari payload,
        // 3. terakhir generate evt_<ulid>
        $eventId = $validated['event_id']
            ?? $this->extractEventId($payload)
            ?? ('evt_' . Str::ulid());

        // type:
        // 1. pakai dari request tervalidasi (jika ada),
        // 2. fallback ke hasil ekstraksi dari payload,
        // 3. jika tetap null, passing string kosong ke processor
        $type = $validated['type'] ?? $this->extractType($payload) ?? '';

        $result = $this->processor->process(
            $provider,
            $eventId,
            $type,
            $rawBody,
            $payload
        );

        return response()->json([
            'data' => [
                'event' => [
                    'provider' => $provider,
                    'event_id' => $eventId,
                    'type'     => $type !== '' ? $type : null,
                ],
                'result' => $result,
            ],
        ], 202);
    }

    /**
     * Ambil Content-Type dari Request.
     *
     * Menggunakan header Request (idiomatik Laravel) dengan fallback ke server vars.
     */
    private function detectContentType(WebhookRequest $request): string
    {
        $header = $request->header('Content-Type');

        if (\is_string($header) && $header !== '') {
            return $header;
        }

        // Fallback defensif (kalau ada proxy/stack khusus)
        return (string) (
            $request->server('CONTENT_TYPE')
            ?? $request->server('HTTP_CONTENT_TYPE')
            ?? ''
        );
    }

    /** Parse payload sesuai content-type. */
    private function parsePayload(string $rawBody, ?string $contentType): array
    {
        $ct = \strtolower((string) $contentType);

        if (\str_contains($ct, 'application/json')) {
            $arr = \json_decode($rawBody, true);

            return \is_array($arr) ? $arr : [];
        }

        if (\str_contains($ct, 'application/x-www-form-urlencoded')) {
            $out = [];
            \parse_str($rawBody, $out);

            return \is_array($out) ? $out : [];
        }

        return [];
    }

    private function extractEventId(array $p): ?string
    {
        foreach ([
            Arr::get($p, 'id'),
            Arr::get($p, 'event_id'),
            Arr::get($p, 'data.id'),
            Arr::get($p, 'resource.id'),
            Arr::get($p, 'object.id'),
        ] as $v) {
            if (\is_string($v) && $v !== '') {
                return $v;
            }
        }

        return null;
    }

    private function extractType(array $p): ?string
    {
        foreach ([
            Arr::get($p, 'type'),
            Arr::get($p, 'event'),
            Arr::get($p, 'data.object'),
        ] as $v) {
            if (\is_string($v) && $v !== '') {
                return $v;
            }
        }

        return null;
    }
}
