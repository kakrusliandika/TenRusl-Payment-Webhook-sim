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
        $hasProvider = Schema::hasColumn('payments', 'provider');
        $hasProviderRef = Schema::hasColumn('payments', 'provider_ref');
        $hasMeta = Schema::hasColumn('payments', 'meta');

        Schema::table('payments', function (Blueprint $table) use ($hasProvider, $hasProviderRef, $hasMeta): void {
            // IMPORTANT (SQLite): ADD COLUMN NOT NULL must have non-NULL default.
            if (! $hasProvider) {
                $table->string('provider', 32)->default('mock')->index();
            }

            if (! $hasProviderRef) {
                $table->string('provider_ref', 191)->nullable()->index();
            }

            // Tambah meta tanpa menghapus metadata legacy.
            if (! $hasMeta) {
                $table->json('meta')->nullable();
            }
        });

        // Backfill defensif
        if (Schema::hasColumn('payments', 'provider')) {
            DB::statement("UPDATE payments SET provider = 'mock' WHERE provider IS NULL OR provider = ''");
        }

        // Migrasi data: metadata -> meta (best effort)
        if (Schema::hasColumn('payments', 'metadata') && Schema::hasColumn('payments', 'meta')) {
            DB::statement('UPDATE payments SET meta = metadata WHERE meta IS NULL');
        }

        // Indexes (guarded)
        Schema::table('payments', function (Blueprint $table): void {
            if (! self::indexExists('payments', 'payments_provider_index') && Schema::hasColumn('payments', 'provider')) {
                $table->index('provider', 'payments_provider_index');
            }

            if (! self::indexExists('payments', 'payments_provider_ref_index') && Schema::hasColumn('payments', 'provider_ref')) {
                $table->index('provider_ref', 'payments_provider_ref_index');
            }

            if (
                ! self::indexExists('payments', 'payments_provider_provider_ref_unique')
                && Schema::hasColumn('payments', 'provider')
                && Schema::hasColumn('payments', 'provider_ref')
            ) {
                $table->unique(['provider', 'provider_ref'], 'payments_provider_provider_ref_unique');
            }
        });
    }

    public function down(): void
    {
        /**
         * Down dibuat “konservatif” untuk menghindari data loss pada environment
         * yang sejak awal sudah memiliki kolom-kolom ini di migration create_payments_table.
         * Rollback cukup menghapus index yang mungkin dibuat oleh migration ini.
         */

        // rollback data meta -> metadata (best effort)
        if (Schema::hasColumn('payments', 'metadata') && Schema::hasColumn('payments', 'meta')) {
            DB::statement('UPDATE payments SET metadata = meta WHERE metadata IS NULL');
        }

        Schema::table('payments', function (Blueprint $table): void {
            if (self::indexExists('payments', 'payments_provider_provider_ref_unique')) {
                $table->dropUnique('payments_provider_provider_ref_unique');
            }

            if (self::indexExists('payments', 'payments_provider_index')) {
                $table->dropIndex('payments_provider_index');
            }

            if (self::indexExists('payments', 'payments_provider_ref_index')) {
                $table->dropIndex('payments_provider_ref_index');
            }
        });
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
