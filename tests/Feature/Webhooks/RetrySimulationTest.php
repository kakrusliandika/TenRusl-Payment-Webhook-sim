<?php

declare(strict_types=1);

use App\Jobs\ProcessWebhookEvent;
use App\Models\WebhookEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;

uses(RefreshDatabase::class);

it('increments attempts and schedules next_retry_at when retry command runs', function () {
    Bus::fake();

    // Baseline pakai detik (timestamp) supaya aman dari mismatch microseconds.
    $baseline = Carbon::createFromTimestamp(Carbon::now()->timestamp);

    /** @var WebhookEvent $event */
    $event = WebhookEvent::factory()->create([
        'provider' => 'mock',
        'event_id' => 'evt_retry_'.now()->timestamp,
        'event_type' => 'payment.paid',
        'status' => 'received',
        'payment_status' => 'pending',
        'attempts' => 1,
        'processed_at' => null,
        'next_retry_at' => null, // eligible (due)
        'last_attempt_at' => $baseline->copy()->subMinutes(2),
        'received_at' => $baseline->copy()->subMinutes(2),
    ]);

    Artisan::call('tenrusl:webhooks:retry', [
        '--provider' => 'mock',
        '--limit' => 1,
        '--max-attempts' => 5,
        '--mode' => 'full',
        '--queue' => true,
    ]);

    Bus::assertDispatched(ProcessWebhookEvent::class, 1);

    $event->refresh();

    // Karena awal attempts=1, setelah “claim” harus jadi 2 (atau lebih)
    expect((int) $event->attempts)->toBeGreaterThanOrEqual(2);

    // next_retry_at harus di-set. Cek menggunakan timestamp (detik) biar tidak kejebak microseconds.
    expect($event->next_retry_at)->not()->toBeNull();

    $next = Carbon::parse((string) $event->next_retry_at);
    expect($next->timestamp)->toBeGreaterThanOrEqual($baseline->timestamp);
});

it('stops retrying when attempts reached the provided max (no claim, no dispatch)', function () {
    Bus::fake();

    /** @var WebhookEvent $event */
    $event = WebhookEvent::factory()->create([
        'provider' => 'mock',
        'event_id' => 'evt_max_'.now()->timestamp,
        'event_type' => 'payment.paid',
        'status' => 'received',
        'payment_status' => 'pending',
        'attempts' => 1, // sudah mencapai max-attempts=1 (lihat Artisan di bawah)
        'processed_at' => null,
        'next_retry_at' => Carbon::now()->subMinute(), // due, tapi harus di-skip karena attempts sudah limit
        'last_attempt_at' => Carbon::now()->subMinutes(2),
        'received_at' => Carbon::now()->subMinutes(2),
    ]);

    Artisan::call('tenrusl:webhooks:retry', [
        '--provider' => 'mock',
        '--limit' => 10,
        '--max-attempts' => 1,
        '--mode' => 'full',
        '--queue' => true,
    ]);

    Bus::assertNotDispatched(ProcessWebhookEvent::class);

    $event->refresh();

    // Tidak berubah
    expect((int) $event->attempts)->toBe(1);
});
