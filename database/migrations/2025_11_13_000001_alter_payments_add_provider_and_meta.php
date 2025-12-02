<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $hasProvider    = Schema::hasColumn('payments', 'provider');
        $hasProviderRef = Schema::hasColumn('payments', 'provider_ref');
        $hasMeta        = Schema::hasColumn('payments', 'meta');

        Schema::table('payments', function (Blueprint $table) use ($hasProvider, $hasProviderRef, $hasMeta) {
            // IMPORTANT (SQLite): ADD COLUMN NOT NULL must have non-NULL default.
            if (! $hasProvider) {
                // Default 'mock' agar existing rows aman & sesuai simulator.
                $table->string('provider', 20)->default('mock');
            }

            if (! $hasProviderRef) {
                $table->string('provider_ref', 150)->nullable();
            }

            // Kode memakai "meta" (JSON). Tambah tanpa menghapus "metadata" legacy.
            if (! $hasMeta) {
                $table->json('meta')->nullable();
            }
        });

        // Pastikan existing rows punya provider non-empty (defensif)
        if (Schema::hasColumn('payments', 'provider')) {
            DB::statement("UPDATE payments SET provider = 'mock' WHERE provider IS NULL OR provider = ''");
        }

        // Migrasi data: metadata -> meta (jika metadata ada)
        if (Schema::hasColumn('payments', 'metadata') && Schema::hasColumn('payments', 'meta')) {
            DB::statement('UPDATE payments SET meta = metadata WHERE meta IS NULL');
        }

        // Indexes (guarded)
        Schema::table('payments', function (Blueprint $table) {
            if (! self::indexExists('payments', 'payments_provider_index')) {
                $table->index('provider', 'payments_provider_index');
            }

            if (! self::indexExists('payments', 'payments_provider_ref_index')) {
                $table->index('provider_ref', 'payments_provider_ref_index');
            }

            if (! self::indexExists('payments', 'payments_provider_provider_ref_unique')) {
                $table->unique(['provider', 'provider_ref'], 'payments_provider_provider_ref_unique');
            }
        });
    }

    public function down(): void
    {
        // rollback data meta -> metadata (opsional)
        if (Schema::hasColumn('payments', 'metadata') && Schema::hasColumn('payments', 'meta')) {
            DB::statement('UPDATE payments SET metadata = meta WHERE metadata IS NULL');
        }

        Schema::table('payments', function (Blueprint $table) {
            if (self::indexExists('payments', 'payments_provider_provider_ref_unique')) {
                $table->dropUnique('payments_provider_provider_ref_unique');
            }

            if (self::indexExists('payments', 'payments_provider_index')) {
                $table->dropIndex('payments_provider_index');
            }

            if (self::indexExists('payments', 'payments_provider_ref_index')) {
                $table->dropIndex('payments_provider_ref_index');
            }

            if (Schema::hasColumn('payments', 'meta')) {
                $table->dropColumn('meta');
            }

            if (Schema::hasColumn('payments', 'provider_ref')) {
                $table->dropColumn('provider_ref');
            }

            if (Schema::hasColumn('payments', 'provider')) {
                $table->dropColumn('provider');
            }
        });
    }

    /**
     * Cek keberadaan index tanpa Doctrine DBAL.
     * (SQLite bisa pakai PRAGMA index_list) :contentReference[oaicite:1]{index=1}
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
        } catch (\Throwable $e) {
            return false;
        }
    }
};
