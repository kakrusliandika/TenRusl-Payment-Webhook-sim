<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_events', function (Blueprint $table) {
            /*
             * Primary key ULID (string).
             * Cocok untuk simulator + gampang di log/trace.
             */
            $table->ulid('id')->primary();

            /*
             * Dedup key (HARUS "keras" di DB):
             * Kombinasi provider + event_id wajib unik.
             */
            $table->string('provider', 32)->index();   // mock | xendit | midtrans | dst.
            $table->string('event_id', 150);           // id event dari provider (atau hash jika provider tidak punya id)

            // Optional: tipe event untuk audit / debugging
            $table->string('event_type', 120)->nullable();

            /*
             * Simpan jejak verifikasi signature (opsional).
             * Bisa berupa hash dari signature header atau fingerprint verifikasi untuk audit.
             */
            $table->string('signature_hash', 128)->nullable();

            /*
             * Raw body + parsed payload.
             * - payload_raw: penting untuk audit dan verifikasi signature yang butuh raw body.
             * - payload: hasil parsing JSON (atau bentuk lain yang kamu normalisasi).
             */
            $table->longText('payload_raw')->nullable();
            $table->json('payload');

            /*
             * Status event internal:
             * - received   : event baru dicatat
             * - processed  : sudah diproses dan (jika ada) payment berhasil diupdate
             * - failed     : pemrosesan gagal (akan retry jika eligible)
             */
            $table->string('status', 20)->default('received')->index();

            /*
             * Status payment hasil infer (pending|succeeded|failed) untuk audit/troubleshooting.
             * Jangan tumpang tindih makna dengan `status` event.
             */
            $table->string('payment_status', 20)->nullable()->index();

            /*
             * Referensi payment di simulator yang terkait webhook ini.
             * Berguna untuk tracing: webhook -> payment.
             */
            $table->string('payment_provider_ref', 191)->nullable()->index();

            /*
             * Attempt & retry scheduling
             *
             * attempts:
             * - Dinaikkan setiap kali event diproses (inline / job / retry command).
             * - Retry command hanya ambil event yang "due" dan attempts < max.
             */
            $table->unsignedSmallInteger('attempts')->default(0);

            // Timestamp-timestamp audit
            $table->timestamp('received_at')->nullable()->index();
            $table->timestamp('last_attempt_at')->nullable()->index();
            $table->timestamp('processed_at')->nullable()->index();

            // Scheduler/command memproses jika next_retry_at <= now (atau null)
            $table->timestamp('next_retry_at')->nullable()->index();

            // Simpan error terakhir (kalau gagal)
            $table->text('error_message')->nullable();

            $table->timestamps();

            /*
             * Dedup kuat (enforced):
             * Laravel mendukung multi-column unique index seperti ini.
             */
            $table->unique(['provider', 'event_id'], 'webhook_events_provider_event_unique');

            /*
             * (Opsional, tapi bagus untuk performa retry selection)
             * Jika query retry sering pakai filter provider + next_retry_at:
             */
            $table->index(['provider', 'next_retry_at'], 'webhook_events_provider_next_retry_idx');

            /*
             * Index tambahan untuk Admin List + Retry Dashboard:
             * - Banyak UI admin akan filter berdasarkan status/pending/fail dan sort by created_at.
             * - Banyak query retry engine pakai status + payment_status + next_retry_at.
             */
            $table->index(['status', 'created_at'], 'webhook_events_status_created_at_idx');
            $table->index(['payment_status', 'created_at'], 'webhook_events_paystatus_created_at_idx');

            /*
             * Composite index untuk pemilihan event yang eligible di-retry:
             * - status != processed (biasanya via where)
             * - payment_status pending
             * - next_retry_at due/null
             *
             * Catatan: meski query bisa memanfaatkan index single-column yang sudah ada,
             * index gabungan ini menjaga performa saat row bertambah banyak.
             */
            $table->index(
                ['status', 'payment_status', 'next_retry_at'],
                'webhook_events_status_paystatus_next_retry_idx'
            );

            // Bisa juga dipertimbangkan (kalau perlu):
            // $table->index(['payment_provider_ref', 'created_at'], 'webhook_events_payref_created_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
    }
};
