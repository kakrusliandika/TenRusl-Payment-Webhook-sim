<?php

declare(strict_types=1);

use App\Http\Middleware\VerifyWebhookSignature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function Pest\Laravel\post;

uses(TestCase::class, RefreshDatabase::class);

it('accepts Skrill webhook (form-encoded) with (bypassed) signature verification and returns 202', function () {
    /** @var TestCase $this */
    $this->withoutMiddleware(VerifyWebhookSignature::class);

    $form = [
        'transaction_id' => 'sk_'.now()->timestamp,
        'merchant_id' => '123456',
        'mb_amount' => '100.00',
        'mb_currency' => 'EUR',
        'status' => '2',
        'md5sig' => 'dummy', // tidak dipakai karena middleware dibypass
    ];

    $resp = post('/api/v1/webhooks/skrill', $form, [
        'Content-Type' => 'application/x-www-form-urlencoded',
    ]);

    $resp->assertStatus(202)
        ->assertJsonStructure([
            'data' => [
                'event' => ['provider', 'event_id', 'type'],
                'result' => ['duplicate', 'persisted', 'status'],
            ],
        ])
        ->assertJsonPath('data.event.provider', 'skrill');
});

it('rejects Skrill webhook with missing/invalid md5sig and returns 401', function () {
    $form = [
        'transaction_id' => 'sk_invalid',
        'merchant_id' => '123456',
        'mb_amount' => '100.00',
        'mb_currency' => 'EUR',
        'status' => '2',
        // signature sengaja salah/invalid
        'md5sig' => 'invalid',
    ];

    $resp = post('/api/v1/webhooks/skrill', $form, [
        'Content-Type' => 'application/x-www-form-urlencoded',
    ]);

    $resp->assertStatus(401);
});
