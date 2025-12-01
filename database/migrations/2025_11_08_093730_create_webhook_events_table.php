<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_events', function (Blueprint $table) {
            // ULID primary key (string)
            $table->ulid('id')->primary();

            $table->string('provider', 32);
            $table->string('event_id', 150);

            $table->string('event_type', 120)->nullable();

            // Simpan jejak verifikasi (opsional) untuk audit
            $table->string('signature_hash', 128)->nullable();

            // Raw body + parsed JSON payload
            $table->longText('payload_raw')->nullable();
            $table->json('payload');

            // received | processed | failed
            $table->string('status', 20)->default('received')->index();

            // Status payment hasil infer (succeeded|failed|pending) untuk audit/troubleshooting
            $table->string('payment_status', 20)->nullable()->index();
            $table->string('payment_provider_ref', 191)->nullable()->index();

            // Attempt & retry scheduling
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->timestamp('received_at')->nullable()->index();
            $table->timestamp('last_attempt_at')->nullable()->index();
            $table->timestamp('processed_at')->nullable()->index();
            $table->timestamp('next_retry_at')->nullable()->index();

            $table->text('error_message')->nullable();

            $table->timestamps();

            // Dedup kuat: kombinasi provider + event_id harus unik
            $table->unique(['provider', 'event_id'], 'webhook_events_provider_event_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
    }
};
