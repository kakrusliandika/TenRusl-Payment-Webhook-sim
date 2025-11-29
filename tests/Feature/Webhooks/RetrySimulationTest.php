<?php

declare(strict_types=1);

use App\Models\WebhookEvent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;

it('increments attempts and schedules next_retry_at when retry command runs', function () {
    // Konfigurasi: maksimum percobaan yang wajar untuk test
    config(['tenrusl.max_retry_attempts' => 5]);

    // Buat event yang eligible untuk retry (next_retry_at sudah lewat)
    /** @var \App\Models\WebhookEvent $event */
    $event = WebhookEvent::factory()->create([
        'provider' => 'mock',
        'event_id' => 'evt_retry_'.now()->timestamp,
        'event_type' => 'payment.failed',
        'payload_raw' => json_encode(['foo' => 'bar']),
        'attempts' => 0,
        'processed_at' => null,
        'next_retry_at' => Carbon::now()->subMinute(),
    ]);

    // Jalankan perintah konsol retry
    Artisan::call('tenrusl:webhooks:retry');

    $event->refresh();

    expect($event->attempts)->toBe(1)
        ->and($event->next_retry_at)->not()->toBeNull()
        ->and($event->next_retry_at->gt(Carbon::now()))->toBeTrue();
});

it('stops retrying when attempts reached the configured max', function () {
    config(['tenrusl.max_retry_attempts' => 1]);

    /** @var \App\Models\WebhookEvent $event */
    $event = WebhookEvent::factory()->create([
        'provider' => 'mock',
        'event_id' => 'evt_max_'.now()->timestamp,
        'event_type' => 'payment.failed',
        'payload_raw' => '{}',
        'attempts' => 1, // sudah mencapai max
        'processed_at' => null,
        'next_retry_at' => Carbon::now()->subMinute(),
    ]);

    Artisan::call('tenrusl:webhooks:retry');

    $event->refresh();

    // Tidak bertambah lagi
    expect($event->attempts)->toBe(1);
});
