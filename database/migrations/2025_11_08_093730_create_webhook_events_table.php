<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_events', function (Blueprint $table): void {
            /*
             * Primary key ULID (string).
             */
            $table->ulid('id')->primary();

            /*
             * Dedup keras: provider + event_id unik.
             */
            $table->string('provider', 32)->index();
            $table->string('event_id', 150);

            /*
             * Tipe event (audit/debug).
             */
            $table->string('event_type', 120)->nullable();

            /*
             * Hash ringkas signature (tanpa bocor secret).
             */
            $table->string('signature_hash', 128)->nullable();
            $table->index('signature_hash', 'webhook_events_signature_hash_idx');

            /*
             * Audit tambahan (tanpa secret):
             * - source_ip   : untuk incident response
             * - request_id  : correlation id dari middleware
             * - headers     : subset header penting (sanitized)
             */
            $table->string('source_ip', 45)->nullable()->index();
            $table->string('request_id', 120)->nullable()->index();
            $table->json('headers')->nullable();

            /*
             * Raw body + parsed payload.
             */
            $table->longText('payload_raw')->nullable();
            $table->json('payload');

            /*
             * Status event internal:
             * received | processed | failed
             */
            $table->string('status', 20)->default('received')->index();

            /*
             * Status payment hasil infer (pending|succeeded|failed).
             */
            $table->string('payment_status', 20)->nullable()->index();

            /*
             * Referensi payment terkait.
             */
            $table->string('payment_provider_ref', 191)->nullable()->index();

            /*
             * attempts:
             * - default 0 saat diterima
             * - naik setiap kali proses (inline/job/retry) berjalan
             */
            $table->unsignedSmallInteger('attempts')->default(0);

            /*
             * Timestamp audit
             */
            $table->timestamp('received_at')->useCurrent()->index();
            $table->timestamp('last_attempt_at')->nullable()->index();
            $table->timestamp('processed_at')->nullable()->index();
            $table->timestamp('next_retry_at')->nullable()->index();

            $table->text('error_message')->nullable();

            $table->timestamps();

            /*
             * Dedup kuat.
             */
            $table->unique(['provider', 'event_id'], 'webhook_events_provider_event_unique');

            /*
             * Index untuk retry selection & dashboard.
             */
            $table->index(['provider', 'next_retry_at'], 'webhook_events_provider_next_retry_idx');
            $table->index(['status', 'created_at'], 'webhook_events_status_created_at_idx');
            $table->index(['payment_status', 'created_at'], 'webhook_events_paystatus_created_at_idx');
            $table->index(
                ['status', 'payment_status', 'next_retry_at'],
                'webhook_events_status_paystatus_next_retry_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
    }
};
