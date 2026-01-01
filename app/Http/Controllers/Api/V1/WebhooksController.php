<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\WebhookRequest;
use App\Services\Webhooks\WebhookProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * WebhooksController (API)
 * -----------------------
 * Endpoint:
 * - POST /api/webhooks/{provider}
 * - POST /api/v1/webhooks/{provider}
 *
 * Catatan:
 * - Signature verification dicegat oleh middleware 'verify.webhook.signature'.
 * - Controller ini fokus:
 *   1) raw body (untuk signature) tetap original
 *   2) parse payload (untuk pemrosesan)
 *   3) normalisasi event_id + event_type
 *   4) delegasi ke WebhookProcessor (dedup + enqueue/process)
 */
class WebhooksController extends Controller
{
    public function __construct(private readonly WebhookProcessor $processor) {}

    public function receive(WebhookRequest $request, string $provider): JsonResponse
    {
        $provider = strtolower(trim($provider));

        // 1) Raw body HARUS original (untuk signature). Jangan pernah json_encode ulang untuk signature.
        $rawBody = $this->resolveRawBody($request);

        // 2) Parse payload tanpa mengubah raw body
        $contentType = $this->detectContentType($request);
        [$payload, $parseError] = $this->parsePayload($rawBody, $contentType);

        if ($parseError !== null) {
            return response()->json([
                'error' => [
                    'code' => 'invalid_payload',
                    'message' => $parseError,
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        $validated = $request->validated();

        // 3) event_id & type: provider-first rules (baru generic fallback)
        [$eventId, $eventIdSource, $generated] = $this->resolveEventId($provider, $payload, $request, $rawBody);

        $type = $this->firstNonEmptyString(
            $validated['type'] ?? null,
            $this->resolveEventType($provider, $payload, $request),
            $this->extractTypeGeneric($payload),
            null
        );

        // Tandai bila event_id hasil generate/fallback agar audit jelas (raw tetap tersimpan di payload_raw)
        if ($generated) {
            $payload['_tenrusl'] = array_merge(
                is_array($payload['_tenrusl'] ?? null) ? $payload['_tenrusl'] : [],
                [
                    'event_id_source' => $eventIdSource,
                    'event_id_generated' => true,
                ]
            );
        } else {
            $payload['_tenrusl'] = array_merge(
                is_array($payload['_tenrusl'] ?? null) ? $payload['_tenrusl'] : [],
                [
                    'event_id_source' => $eventIdSource,
                    'event_id_generated' => false,
                ]
            );
        }

        // 4) Proses (dedup + enqueue/process)
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
                    'type' => $type,
                    'event_id_source' => $eventIdSource,
                    'event_id_generated' => $generated,
                ],
                'result' => $result,
            ],
        ], Response::HTTP_ACCEPTED);
    }

    private function resolveRawBody(WebhookRequest $request): string
    {
        $rawBody = '';

        if (method_exists($request, 'rawBody')) {
            /** @var mixed $rb */
            $rb = $request->rawBody();
            $rawBody = is_string($rb) ? $rb : '';
        }

        if ($rawBody === '') {
            /** @var mixed $attr */
            $attr = $request->attributes->get('tenrusl_raw_body', '');
            $rawBody = is_string($attr) ? $attr : '';
        }

        if ($rawBody === '') {
            $rawBody = (string) $request->getContent();
        }

        return $rawBody;
    }

    private function detectContentType(WebhookRequest $request): string
    {
        $header = $request->header('Content-Type');

        if (is_string($header) && $header !== '') {
            return $header;
        }

        return (string) (
            $request->server('CONTENT_TYPE')
            ?? $request->server('HTTP_CONTENT_TYPE')
            ?? ''
        );
    }

    /**
     * @return array{0: array, 1: string|null} [payload, parseError]
     */
    private function parsePayload(string $rawBody, ?string $contentType): array
    {
        $ct = strtolower((string) $contentType);
        $trim = trim($rawBody);

        if ($trim === '') {
            return [[], null];
        }

        // Jika header Content-Type kosong/tidak akurat tapi body terlihat JSON
        $looksJson = str_starts_with($trim, '{') || str_starts_with($trim, '[');

        if (str_contains($ct, 'application/json') || $looksJson) {
            $decoded = json_decode($rawBody, true);

            if (! is_array($decoded)) {
                return [[], 'Invalid JSON payload'];
            }

            return [$decoded, null];
        }

        if (str_contains($ct, 'application/x-www-form-urlencoded')) {
            $out = [];
            parse_str($rawBody, $out);

            return [is_array($out) ? $out : [], null];
        }

        return [[], null];
    }

    /**
     * Resolve event_id:
     * - Utamakan field resmi per provider.
     * - Kalau tidak ada, fallback deterministik pakai hash(rawBody) agar retry body yang sama tetap dedup.
     *
     * @return array{0: string, 1: string, 2: bool} [eventId, source, generated]
     */
    private function resolveEventId(string $provider, array $payload, WebhookRequest $request, string $rawBody): array
    {
        // Provider-specific primary sources
        $candidates = match ($provider) {
            // Stripe Event: id
            'stripe' => [
                Arr::get($payload, 'id'),
            ],

            // PayPal Webhook: id
            'paypal' => [
                Arr::get($payload, 'id'),
            ],

            // Paddle Billing: event_id/id (variasi)
            'paddle' => [
                Arr::get($payload, 'event_id'),
                Arr::get($payload, 'id'),
            ],

            // Midtrans: tidak selalu punya event id; transaksi biasanya unik
            'midtrans' => [
                Arr::get($payload, 'transaction_id'),
                Arr::get($payload, 'order_id'),
            ],

            // Xendit: umumnya id
            'xendit' => [
                Arr::get($payload, 'id'),
                Arr::get($payload, 'event_id'),
            ],

            default => [
                Arr::get($payload, 'id'),
                Arr::get($payload, 'event_id'),
            ],
        };

        foreach ($candidates as $v) {
            if (is_string($v) && trim($v) !== '') {
                return [trim($v), 'payload', false];
            }
        }

        // Generic deep fallback (nested ids)
        foreach ([
            Arr::get($payload, 'data.id'),
            Arr::get($payload, 'resource.id'),
            Arr::get($payload, 'object.id'),
            Arr::get($payload, 'data.object.id'),
            Arr::get($payload, 'data.event_id'),
        ] as $v) {
            if (is_string($v) && trim($v) !== '') {
                return [trim($v), 'payload_nested', false];
            }
        }

        // Header fallback (kalau provider pakai request-id / event-id)
        foreach ([
            'X-Event-Id',
            'X-Request-Id',
            'Request-Id',
        ] as $h) {
            $hv = $request->headers->get($h);
            if (is_string($hv) && trim($hv) !== '') {
                return [trim($hv), 'header:' . strtolower($h), false];
            }
        }

        // Deterministic generated id: raw body hash (agar retry bytes sama tetap dedup)
        $hash = hash('sha256', $rawBody);

        return ['gen_' . substr($hash, 0, 32), 'generated_body_hash', true];
    }

    private function resolveEventType(string $provider, array $payload, WebhookRequest $request): ?string
    {
        // Provider-specific preference
        $candidates = match ($provider) {
            'stripe' => [
                Arr::get($payload, 'type'),
            ],
            'paypal' => [
                Arr::get($payload, 'event_type'),
                Arr::get($payload, 'type'),
            ],
            'midtrans' => [
                Arr::get($payload, 'transaction_status'),
                Arr::get($payload, 'status'),
            ],
            default => [
                Arr::get($payload, 'type'),
                Arr::get($payload, 'event_type'),
                Arr::get($payload, 'event'),
            ],
        };

        foreach ($candidates as $v) {
            if (is_string($v) && trim($v) !== '') {
                return trim($v);
            }
        }

        // Header fallback
        $hv = $request->headers->get('X-Event-Type');
        if (is_string($hv) && trim($hv) !== '') {
            return trim($hv);
        }

        return null;
    }

    private function extractTypeGeneric(array $p): ?string
    {
        foreach ([
            Arr::get($p, 'type'),
            Arr::get($p, 'event'),
            Arr::get($p, 'event_type'),
            Arr::get($p, 'data.type'),
        ] as $v) {
            if (is_string($v) && trim($v) !== '') {
                return trim($v);
            }
        }

        return null;
    }

    private function firstNonEmptyString(mixed ...$values): ?string
    {
        foreach ($values as $v) {
            if (is_string($v)) {
                $t = trim($v);
                if ($t !== '') {
                    return $t;
                }
            }
        }

        return null;
    }
}
