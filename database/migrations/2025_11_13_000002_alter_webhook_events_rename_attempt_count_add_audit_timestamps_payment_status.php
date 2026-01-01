<?php

declare(strict_types=1);

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

        // Tambahan audit fields
        $hasSignatureHash = Schema::hasColumn('webhook_events', 'signature_hash');
        $hasSourceIp = Schema::hasColumn('webhook_events', 'source_ip');
        $hasRequestId = Schema::hasColumn('webhook_events', 'request_id');
        $hasHeaders = Schema::hasColumn('webhook_events', 'headers');

        Schema::table('webhook_events', function (Blueprint $table) use (
            $hasReceivedAt,
            $hasLastAttemptAt,
            $hasProcessedAt,
            $hasPaymentStatus,
            $hasPaymentProviderRef,
            $hasAttempts,
            $hasSignatureHash,
            $hasSourceIp,
            $hasRequestId,
            $hasHeaders,
        ): void {
            // Audit & status fields yang diharapkan kode
            if (! $hasReceivedAt) {
                $table->timestamp('received_at')->nullable()->index();
            }

            if (! $hasLastAttemptAt) {
                $table->timestamp('last_attempt_at')->nullable()->index();
            }

            if (! $hasProcessedAt) {
                $table->timestamp('processed_at')->nullable()->index();
            }

            if (! $hasPaymentStatus) {
                $table->string('payment_status', 20)->nullable()->index();
            }

            if (! $hasPaymentProviderRef) {
                $table->string('payment_provider_ref', 191)->nullable()->index();
            }

            // attempts (target akhir)
            if (! $hasAttempts) {
                $table->unsignedSmallInteger('attempts')->default(0);
            }

            // Audit tambahan
            if (! $hasSignatureHash) {
                $table->string('signature_hash', 128)->nullable();
            }

            if (! $hasSourceIp) {
                $table->string('source_ip', 45)->nullable();
            }

            if (! $hasRequestId) {
                $table->string('request_id', 120)->nullable();
            }

            if (! $hasHeaders) {
                $table->json('headers')->nullable();
            }
        });

        // Index guarded untuk audit tambahan
        Schema::table('webhook_events', function (Blueprint $table) use (
            $hasSignatureHash,
            $hasSourceIp,
            $hasRequestId
        ): void {
            if (! $hasSignatureHash) {
                if (! self::indexExists('webhook_events', 'webhook_events_signature_hash_idx')) {
                    $table->index('signature_hash', 'webhook_events_signature_hash_idx');
                }
            }

            if (! $hasSourceIp) {
                if (! self::indexExists('webhook_events', 'webhook_events_source_ip_index')) {
                    $table->index('source_ip', 'webhook_events_source_ip_index');
                }
            }

            if (! $hasRequestId) {
                if (! self::indexExists('webhook_events', 'webhook_events_request_id_index')) {
                    $table->index('request_id', 'webhook_events_request_id_index');
                }
            }
        });

        // Migrasi data attempt_count -> attempts (kalau attempt_count ada)
        if ($hasAttemptCount && Schema::hasColumn('webhook_events', 'attempts')) {
            DB::statement('UPDATE webhook_events SET attempts = attempt_count WHERE attempts = 0');

            // Best-effort drop kolom lama (jangan bikin migrasi gagal).
            try {
                Schema::table('webhook_events', function (Blueprint $table): void {
                    $table->dropColumn('attempt_count');
                });
            } catch (\Throwable) {
                // noop
            }
        }
    }

    public function down(): void
    {
        /**
         * Down dibuat “konservatif” untuk mencegah data loss pada fresh install
         * yang sudah membawa kolom-kolom ini dari migration create_webhook_events_table.
         *
         * Yang kita lakukan hanya best-effort restore attempt_count (jika dibutuhkan)
         * tanpa menghapus kolom-kolom audit utama.
         */
        $hasAttemptCount = Schema::hasColumn('webhook_events', 'attempt_count');
        $hasAttempts = Schema::hasColumn('webhook_events', 'attempts');

        if (! $hasAttemptCount && $hasAttempts) {
            Schema::table('webhook_events', function (Blueprint $table): void {
                $table->unsignedSmallInteger('attempt_count')->default(0);
            });

            DB::statement('UPDATE webhook_events SET attempt_count = attempts');
        }
    }

    /**
     * Cek keberadaan index tanpa Doctrine DBAL.
     */
    private static function indexExists(string $table, string $indexName): bool
    {
        try {
            $driver = Schema::getConnection()->getDriverName();

            return match ($driver) {
                'sqlite' => collect(DB::select("PRAGMA index_list('$table')"))
                    ->contains(fn ($row) => isset($row->name) && $row->name === $indexName),

                'mysql' => count(DB::select(
                    'SELECT 1
                     FROM information_schema.statistics
                     WHERE table_schema = DATABASE()
                       AND table_name = ?
                       AND index_name = ?
                     LIMIT 1',
                    [$table, $indexName]
                )) > 0,

                'pgsql' => count(DB::select(
                    'SELECT 1
                     FROM pg_indexes
                     WHERE tablename = ?
                       AND indexname = ?
                     LIMIT 1',
                    [$table, $indexName]
                )) > 0,

                default => false,
            };
        } catch (\Throwable) {
            return false;
        }
    }
};
