<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\User;
use App\Models\WebhookEvent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * =========================================================
         * 1) Demo Admin User
         * =========================================================
         * Tujuan:
         * - Setelah deploy / migrate:fresh --seed, admin panel tidak “kosong”
         * - Kalau admin UI kamu butuh login user (bukan API key), ini langsung siap.
         *
         * Catatan:
         * - Password: "password"
         * - Email: admin@tenrusl.local
         */
        User::query()->updateOrCreate(
            ['email' => 'admin@tenrusl.local'],
            [
                'name' => 'Admin Demo',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
            ]
        );

        /**
         * =========================================================
         * 2) Seed Payments (agar Admin List ada isi)
         * =========================================================
         * Komposisi demo:
         * - pending   : 6
         * - succeeded : 4
         * - failed    : 3
         */
        $pendingPayments = Payment::factory()
            ->count(6)
            ->pending()
            ->create();

        $succeededPayments = Payment::factory()
            ->count(4)
            ->succeeded()
            ->create();

        $failedPayments = Payment::factory()
            ->count(3)
            ->failed()
            ->create();

        /**
         * =========================================================
         * 3) Seed Webhook Events (agar Admin Retry/Events list ada isi)
         * =========================================================
         * Prinsip:
         * - Sebagian event link ke payment_provider_ref yang ada (biar UI enak dilihat)
         * - Ada event "received" due (eligible retry)
         * - Ada event "failed" due (eligible retry)
         * - Ada event "processed" final (riwayat)
         */

        $now = Carbon::now();

        // Helper untuk bikin payload + raw konsisten (audit-friendly)
        $makePayload = static function (string $provider, string $providerRef, string $type) use ($now): array {
            $eventId = 'evt_seed_'.Str::ulid()->toBase32();

            $payload = [
                'event_id' => $eventId,
                'type' => $type,
                'data' => [
                    'provider' => $provider,
                    'ref' => $providerRef,
                ],
                'sent_at' => $now->toISOString(),
            ];

            $raw = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            if ($raw === false) {
                $raw = '{}';
            }

            return [$eventId, $payload, $raw];
        };

        /**
         * 3A) Received (due) untuk beberapa pending payment
         * - status=received
         * - payment_status=pending
         * - next_retry_at=NULL (eligible)
         */
        foreach ($pendingPayments->take(3) as $p) {
            [$eventId, $payload, $raw] = $makePayload(
                (string) $p->provider,
                (string) $p->provider_ref,
                'payment.pending'
            );

            WebhookEvent::query()->create([
                'id' => (string) Str::ulid(),
                'provider' => (string) $p->provider,
                'event_id' => $eventId,
                'event_type' => (string) ($payload['type'] ?? null),

                'signature_hash' => hash('sha256', 'seed:'.$eventId),
                'payload_raw' => $raw,
                'payload' => $payload,

                'status' => 'received',
                'payment_status' => 'pending',
                'payment_provider_ref' => (string) $p->provider_ref,

                'attempts' => 0,
                'received_at' => $now->copy()->subMinutes(3),
                'last_attempt_at' => null,
                'processed_at' => null,
                'next_retry_at' => null,

                'error_message' => null,
                'created_at' => $now->copy()->subMinutes(3),
                'updated_at' => $now->copy()->subMinutes(3),
            ]);
        }

        /**
         * 3B) Failed (due) untuk 1 pending payment (simulasi perlu retry)
         * - status=failed
         * - payment_status=pending
         * - next_retry_at di masa lalu (eligible)
         */
        $pRetry = $pendingPayments->get(3);
        if ($pRetry) {
            [$eventId, $payload, $raw] = $makePayload(
                (string) $pRetry->provider,
                (string) $pRetry->provider_ref,
                'payment.retry_required'
            );

            WebhookEvent::query()->create([
                'id' => (string) Str::ulid(),
                'provider' => (string) $pRetry->provider,
                'event_id' => $eventId,
                'event_type' => (string) ($payload['type'] ?? null),

                'signature_hash' => hash('sha256', 'seed:'.$eventId),
                'payload_raw' => $raw,
                'payload' => $payload,

                'status' => 'failed',
                'payment_status' => 'pending',
                'payment_provider_ref' => (string) $pRetry->provider_ref,

                'attempts' => 2,
                'received_at' => $now->copy()->subMinutes(10),
                'last_attempt_at' => $now->copy()->subMinutes(4),
                'processed_at' => null,
                'next_retry_at' => $now->copy()->subMinute(), // due

                'error_message' => 'Seeded failure for retry demo',
                'created_at' => $now->copy()->subMinutes(10),
                'updated_at' => $now->copy()->subMinutes(4),
            ]);
        }

        /**
         * 3C) Not-due (untuk demo "scheduled retry")
         * - status=received
         * - payment_status=pending
         * - next_retry_at future (tidak eligible sekarang)
         */
        $pNotDue = $pendingPayments->get(4);
        if ($pNotDue) {
            [$eventId, $payload, $raw] = $makePayload(
                (string) $pNotDue->provider,
                (string) $pNotDue->provider_ref,
                'payment.scheduled_retry'
            );

            WebhookEvent::query()->create([
                'id' => (string) Str::ulid(),
                'provider' => (string) $pNotDue->provider,
                'event_id' => $eventId,
                'event_type' => (string) ($payload['type'] ?? null),

                'signature_hash' => hash('sha256', 'seed:'.$eventId),
                'payload_raw' => $raw,
                'payload' => $payload,

                'status' => 'received',
                'payment_status' => 'pending',
                'payment_provider_ref' => (string) $pNotDue->provider_ref,

                'attempts' => 1,
                'received_at' => $now->copy()->subMinutes(2),
                'last_attempt_at' => $now->copy()->subMinute(),
                'processed_at' => null,
                'next_retry_at' => $now->copy()->addMinutes(10), // not due

                'error_message' => null,
                'created_at' => $now->copy()->subMinutes(2),
                'updated_at' => $now->copy()->subMinute(),
            ]);
        }

        /**
         * 3D) Processed (final history) untuk succeeded payment
         * - status=processed
         * - payment_status=succeeded
         * - processed_at set
         */
        foreach ($succeededPayments->take(2) as $p) {
            [$eventId, $payload, $raw] = $makePayload(
                (string) $p->provider,
                (string) $p->provider_ref,
                'payment.succeeded'
            );

            WebhookEvent::query()->create([
                'id' => (string) Str::ulid(),
                'provider' => (string) $p->provider,
                'event_id' => $eventId,
                'event_type' => (string) ($payload['type'] ?? null),

                'signature_hash' => hash('sha256', 'seed:'.$eventId),
                'payload_raw' => $raw,
                'payload' => $payload,

                'status' => 'processed',
                'payment_status' => 'succeeded',
                'payment_provider_ref' => (string) $p->provider_ref,

                'attempts' => 1,
                'received_at' => $now->copy()->subMinutes(30),
                'last_attempt_at' => $now->copy()->subMinutes(29),
                'processed_at' => $now->copy()->subMinutes(28),
                'next_retry_at' => null,

                'error_message' => null,
                'created_at' => $now->copy()->subMinutes(30),
                'updated_at' => $now->copy()->subMinutes(28),
            ]);
        }

        /**
         * 3E) Processed failed (history) untuk failed payment
         * - status=processed
         * - payment_status=failed
         */
        foreach ($failedPayments->take(1) as $p) {
            [$eventId, $payload, $raw] = $makePayload(
                (string) $p->provider,
                (string) $p->provider_ref,
                'payment.failed'
            );

            WebhookEvent::query()->create([
                'id' => (string) Str::ulid(),
                'provider' => (string) $p->provider,
                'event_id' => $eventId,
                'event_type' => (string) ($payload['type'] ?? null),

                'signature_hash' => hash('sha256', 'seed:'.$eventId),
                'payload_raw' => $raw,
                'payload' => $payload,

                'status' => 'processed',
                'payment_status' => 'failed',
                'payment_provider_ref' => (string) $p->provider_ref,

                'attempts' => 1,
                'received_at' => $now->copy()->subMinutes(40),
                'last_attempt_at' => $now->copy()->subMinutes(39),
                'processed_at' => $now->copy()->subMinutes(38),
                'next_retry_at' => null,

                'error_message' => null,
                'created_at' => $now->copy()->subMinutes(40),
                'updated_at' => $now->copy()->subMinutes(38),
            ]);
        }
    }
}
