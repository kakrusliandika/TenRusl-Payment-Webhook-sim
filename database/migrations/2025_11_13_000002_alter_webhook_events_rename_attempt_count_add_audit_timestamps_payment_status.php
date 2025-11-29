<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('webhook_events', function (Blueprint $table) {
            // Kolom audit & status pembayaran yang diharapkan kode
            if (! Schema::hasColumn('webhook_events', 'received_at')) {
                $table->timestamp('received_at')->nullable()->after('payload');
            }
            if (! Schema::hasColumn('webhook_events', 'last_attempt_at')) {
                $table->timestamp('last_attempt_at')->nullable()->after('received_at');
            }
            if (! Schema::hasColumn('webhook_events', 'processed_at')) {
                $table->timestamp('processed_at')->nullable()->after('last_attempt_at');
            }
            if (! Schema::hasColumn('webhook_events', 'payment_status')) {
                $table->string('payment_status', 20)->nullable()->after('status'); // pending|succeeded|failed
            }
            if (! Schema::hasColumn('webhook_events', 'payment_provider_ref')) {
                $table->string('payment_provider_ref', 150)->nullable()->after('event_id');
            }

            // Tambah kolom attempts (target akhir), kemudian copy dari attempt_count jika ada
            if (! Schema::hasColumn('webhook_events', 'attempts')) {
                $table->unsignedSmallInteger('attempts')->default(0)->after('status');
            }
        });

        // Migrasi data attempt_count -> attempts (lalu drop attempt_count)
        $hasAttemptCount = Schema::hasColumn('webhook_events', 'attempt_count');
        $hasAttempts = Schema::hasColumn('webhook_events', 'attempts');

        if ($hasAttemptCount && $hasAttempts) {
            DB::statement('UPDATE webhook_events SET attempts = attempt_count WHERE attempts = 0');
            // Drop kolom lama agar konsisten dengan kode
            Schema::table('webhook_events', function (Blueprint $table) {
                $table->dropColumn('attempt_count');
            });
        }

        // Index untuk pemrosesan retry terjadwal
        Schema::table('webhook_events', function (Blueprint $table) {
            if (! self::indexExists('webhook_events', 'webhook_events_next_retry_at_index') && Schema::hasColumn('webhook_events', 'next_retry_at')) {
                $table->index('next_retry_at');
            }
        });
    }

    public function down(): void
    {
        // Kembalikan kolom attempt_count bila belum ada, salin balik data dari attempts, lalu hapus kolom baru
        if (! Schema::hasColumn('webhook_events', 'attempt_count') && Schema::hasColumn('webhook_events', 'attempts')) {
            Schema::table('webhook_events', function (Blueprint $table) {
                $table->unsignedSmallInteger('attempt_count')->default(0)->after('status');
            });
            DB::statement('UPDATE webhook_events SET attempt_count = attempts');
        }

        Schema::table('webhook_events', function (Blueprint $table) {
            if (Schema::hasColumn('webhook_events', 'attempts')) {
                $table->dropColumn('attempts');
            }
            if (Schema::hasColumn('webhook_events', 'received_at')) {
                $table->dropColumn('received_at');
            }
            if (Schema::hasColumn('webhook_events', 'last_attempt_at')) {
                $table->dropColumn('last_attempt_at');
            }
            if (Schema::hasColumn('webhook_events', 'processed_at')) {
                $table->dropColumn('processed_at');
            }
            if (Schema::hasColumn('webhook_events', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
            if (Schema::hasColumn('webhook_events', 'payment_provider_ref')) {
                $table->dropColumn('payment_provider_ref');
            }

            if (self::indexExists('webhook_events', 'webhook_events_next_retry_at_index')) {
                $table->dropIndex('webhook_events_next_retry_at_index');
            }
        });
    }

    private static function indexExists(string $table, string $indexName): bool
    {
        try {
            $connection = Schema::getConnection();
            $schema = $connection->getDoctrineSchemaManager();
            $indexes = $schema->listTableIndexes($table);

            return array_key_exists($indexName, $indexes);
        } catch (\Throwable $e) {
            return false;
        }
    }
};
