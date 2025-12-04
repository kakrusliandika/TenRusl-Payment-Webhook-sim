<?php

declare(strict_types=1);

use Illuminate\Testing\TestResponse;

use function Pest\Laravel\call;

/**
 * Helper: pastikan apapun response-nya bisa diperlakukan sebagai TestResponse
 * supaya assertJsonStructure / assertJsonPath tetap aman dipakai.
 */
function asTestResponse(mixed $response): TestResponse
{
    return $response instanceof TestResponse
        ? $response
        : TestResponse::fromBaseResponse($response);
}

beforeEach(function (): void {
    // Secret untuk verifier LemonSqueezy di app kamu (sesuaikan key config kalau berbeda)
    config(['tenrusl.ls_webhook_secret' => 'test_ls_secret']);
});

it('accepts Lemon Squeezy webhook with valid signature and returns 202', function () {
    $payload = [
        'meta' => ['event_name' => 'order_paid'],
        'data' => ['id' => 'ls_'.now()->timestamp, 'attributes' => ['total' => 1000]],
    ];

    // Raw JSON yang benar-benar dipakai buat signature dan dikirim sebagai body request
    $rawBody = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($rawBody === false) {
        throw new \RuntimeException('json_encode failed');
    }

    $secret = (string) config('tenrusl.ls_webhook_secret', 'test_ls_secret');
    $signature = hash_hmac('sha256', $rawBody, $secret); // hex

    // Kirim RAW body supaya signature match 1:1 (jangan postJson karena dia encode ulang)
    $baseResp = call('POST', '/api/v1/webhooks/lemonsqueezy', [], [], [], [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_X_SIGNATURE' => $signature,          // "X-Signature"
        'HTTP_X_LEMONSQUEEZY_SIGNATURE' => $signature, // fallback kalau implementasi kamu beda header
    ], $rawBody);

    $resp = asTestResponse($baseResp);

    $resp->assertStatus(202)
        ->assertJsonStructure([
            'data' => [
                'event' => ['provider', 'event_id', 'type'],
                'result' => ['duplicate', 'persisted', 'status'],
            ],
        ])
        ->assertJsonPath('data.event.provider', 'lemonsqueezy');
});

it('rejects Lemon Squeezy webhook with invalid signature and returns 401', function () {
    $payload = [
        'meta' => ['event_name' => 'order_paid'],
        'data' => ['id' => 'ls_invalid'],
    ];

    $rawBody = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($rawBody === false) {
        throw new \RuntimeException('json_encode failed');
    }

    $baseResp = call('POST', '/api/v1/webhooks/lemonsqueezy', [], [], [], [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_X_SIGNATURE' => 'invalid',
        'HTTP_X_LEMONSQUEEZY_SIGNATURE' => 'invalid',
    ], $rawBody);

    $resp = asTestResponse($baseResp);

    $resp->assertStatus(401);
});
