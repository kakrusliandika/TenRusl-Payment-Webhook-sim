<?php

use App\Models\Payment;
use App\Models\WebhookEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['tenrusl.mock_secret' => 'testsecret']);
});

it('reschedules failed event when payment_id missing', function () {
    // Create a failed event due for retry (no payment_id)
    $evt = WebhookEvent::query()->create([
        'provider' => 'mock',
        'event_id' => 'evt_retry_1',
        'signature_hash' => 'mock:hash',
        'payload' => [
            'event_id' => 'evt_retry_1',
            'type' => 'payment.paid',
            'data' => [], // missing payment_id
        ],
        'status' => 'failed',
        'attempt_count' => 1,
        'next_retry_at' => now()->subSeconds(10),
        'error_message' => 'initial',
    ]);

    // Run command
    $this->artisan('tenrusl:webhooks:retry --limit=10 --max=3')
        ->assertExitCode(0);

    $evt->refresh();
    expect($evt->status)->toBe('failed') // still failed but rescheduled
        ->and($evt->attempt_count)->toBe(2)
        ->and($evt->next_retry_at)->not()->toBeNull();
});

it('processes event successfully and marks payment paid', function () {
    $payment = Payment::factory()->pending()->create([
        'amount' => 10000,
    ]);

    $evt = WebhookEvent::query()->create([
        'provider' => 'mock',
        'event_id' => 'evt_retry_2',
        'signature_hash' => 'mock:hash',
        'payload' => [
            'event_id' => 'evt_retry_2',
            'type' => 'payment.paid',
            'data' => ['payment_id' => (string) $payment->id],
        ],
        'status' => 'failed',
        'attempt_count' => 1,
        'next_retry_at' => now()->subSeconds(10),
        'error_message' => 'previous failure',
    ]);

    $this->artisan('tenrusl:webhooks:retry --limit=10 --max=3')
        ->assertExitCode(0);

    $evt->refresh();
    $payment->refresh();

    expect($evt->status)->toBe('processed')
        ->and($evt->next_retry_at)->toBeNull()
        ->and($payment->status)->toBe('paid');
});
