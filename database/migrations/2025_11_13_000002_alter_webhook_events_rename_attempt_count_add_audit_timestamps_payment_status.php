<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $hasReceivedAt = Schema::hasColumn('webhook_events', 'received_at');
        $hasLastAttemptAt = Schema::hasColumn('webhook_events', 'last_attempt_at');
        $hasProcessedAt = Schema::hasColumn('webhook_events', 'processed_at');
        $hasPaymentStatus = Schema::hasColumn('webhook_events', 'payment_status');
        $hasPaymentProviderRef = Schema::hasColumn('webhook_events', 'payment_provider_ref');
        $hasAttempts = Schema::hasColumn('webhook_events', 'attempts');
        $hasAttemptCount = Schema::hasColumn('webhook_events', 'attempt_count');

        Schema::table('webhook_events', function (Blueprint $table) use (
            $hasReceivedAt,
            $hasLastAttemptAt,
            $hasProcessedAt,
            $hasPaymentStatus,
            $hasPaymentProviderRef,
            $hasAttempts
        ) {
            // Kolom audit & status pembayaran yang diharapkan kode
            if (! $hasReceivedAt) {
                $table->timestamp('received_at')->nullable();
            }

            if (! $hasLastAttemptAt) {
                $table->timestamp('last_attempt_at')->nullable();
            }

            if (! $hasProcessedAt) {
                $table->timestamp('processed_at')->nullable();
            }

            if (! $hasPaymentStatus) {
                // pending|succeeded|failed (nullable untuk kompatibilitas state lama)
                $table->string('payment_status', 20)->nullable();
            }

            if (! $hasPaymentProviderRef) {
                $table->string('payment_provider_ref', 150)->nullable();
            }

            // Kolom attempts (target akhir)
            if (! $hasAttempts) {
                $table->unsignedSmallInteger('attempts')->default(0);
            }
        });

        // Migrasi data attempt_count -> attempts (kalau attempt_count ada)
        if ($hasAttemptCount && Schema::hasColumn('webhook_events', 'attempts')) {
            DB::statement('UPDATE webhook_events SET attempts = attempt_count WHERE attempts = 0');

            // Best-effort: drop kolom lama agar konsisten dengan kode.
            // (Kalau suatu environment tidak mendukung dropColumn, jangan bikin migrasi gagal.)
            try {
                Schema::table('webhook_events', function (Blueprint $table) {
                    $table->dropColumn('attempt_count');
                });
            } catch (\Throwable $e) {
                // noop
            }
        }

        /**
         * PENTING:
         * Jangan bikin index next_retry_at di sini.
         * Di project kamu index itu SUDAH dibuat oleh migration create_webhook_events_table,
         * dan mencoba create lagi akan error "index ... already exists" di SQLite.
         */
    }

    public function down(): void
    {
        // Restore attempt_count bila diperlukan untuk rollback
        $hasAttemptCount = Schema::hasColumn('webhook_events', 'attempt_count');
        $hasAttempts = Schema::hasColumn('webhook_events', 'attempts');

        if (! $hasAttemptCount && $hasAttempts) {
            Schema::table('webhook_events', function (Blueprint $table) {
                $table->unsignedSmallInteger('attempt_count')->default(0);
            });

            DB::statement('UPDATE webhook_events SET attempt_count = attempts');
        }

        Schema::table('webhook_events', function (Blueprint $table) {
            // attempts
            if (Schema::hasColumn('webhook_events', 'attempts')) {
                $table->dropColumn('attempts');
            }

            // kolom audit
            if (Schema::hasColumn('webhook_events', 'received_at')) {
                $table->dropColumn('received_at');
            }
            if (Schema::hasColumn('webhook_events', 'last_attempt_at')) {
                $table->dropColumn('last_attempt_at');
            }
            if (Schema::hasColumn('webhook_events', 'processed_at')) {
                $table->dropColumn('processed_at');
            }

            // payment fields
            if (Schema::hasColumn('webhook_events', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
            if (Schema::hasColumn('webhook_events', 'payment_provider_ref')) {
                $table->dropColumn('payment_provider_ref');
            }
        });

        // Tidak drop index next_retry_at di sini karena bukan migration ini yang membuatnya.
    }
};
