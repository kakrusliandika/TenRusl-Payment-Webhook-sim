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

            $table->string('provider', 20);      // mock | xendit | midtrans | dst.
            $table->string('event_id', 150);     // id event dari provider

            // Simpan jejak verifikasi (HMAC/token/hash) untuk audit
            $table->string('signature_hash', 128)->nullable();

            $table->json('payload');             // payload mentah (json)

            // received | processed | failed
            $table->string('status', 20)->default('received')->index();

            $table->unsignedSmallInteger('attempt_count')->default(0);
            $table->timestamp('next_retry_at')->nullable();
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
