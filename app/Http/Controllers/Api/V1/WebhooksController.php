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
 * - Signature verification seharusnya dicegat oleh middleware 'verify.webhook.signature'
 *   di routes/api.php.
 * - Controller ini fokus:
 *   1) bentuk payload (raw + parsed)
 *   2) tentukan event_id + type
 *   3) delegasikan ke WebhookProcessor (dedup + enqueue/process)
 */
class WebhooksController extends Controller
{
    public function __construct(private readonly WebhookProcessor $processor) {}

    /**
     * POST /api/webhooks/{provider}
     */
    public function receive(WebhookRequest $request, string $provider): JsonResponse
    {
        // Ambil raw body secara defensif (prioritas: request attribute raw body -> getContent()).
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

        // Dari FormRequest (jika rule ada) - tetap aman walau kosong.
        $validated = $request->validated();

        $eventId = $this->firstNonEmptyString(
            $validated['event_id'] ?? null,
            $this->extractEventId($payload),
            'evt_' . (string) Str::ulid()
        );

        $type = $this->firstNonEmptyString(
            $validated['type'] ?? null,
            $this->extractType($payload),
            null
        );

        // Proses (dedup + enqueue) tetap di service
        $result = $this->processor->process(
            $provider,
            (string) $eventId,
            $type,
            $rawBody,
            $payload
        );

        return response()->json([
            'data' => [
                'event' => [
                    'provider' => $provider,
                    'event_id' => (string) $eventId,
                    'type' => $type,
                ],
                'result' => $result,
            ],
        ], Response::HTTP_ACCEPTED);
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

        return (string) (
            $request->server('CONTENT_TYPE')
            ?? $request->server('HTTP_CONTENT_TYPE')
            ?? ''
        );
    }

    /**
     * Parse payload sesuai content-type:
     * - application/json                  => decode json (error kalau invalid)
     * - application/x-www-form-urlencoded => parse_str
     * - lainnya                           => []
     *
     * @return array{0: array, 1: string|null} [payload, parseError]
     */
    private function parsePayload(string $rawBody, ?string $contentType): array
    {
        $ct = strtolower((string) $contentType);

        // Jika body kosong, anggap payload kosong (bukan error).
        if (trim($rawBody) === '') {
            return [[], null];
        }

        if (str_contains($ct, 'application/json')) {
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

        // Unknown content-type: biarkan payload kosong (tetap boleh diproses).
        return [[], null];
    }

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
            if (is_string($v) && trim($v) !== '') {
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
            Arr::get($p, 'event_type'),
            Arr::get($p, 'data.type'),
        ] as $v) {
            if (is_string($v) && trim($v) !== '') {
                return $v;
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
