<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom-kolom yang dipakai kode (tanpa mengubah kolom lama yang sudah ada)
        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'provider')) {
                $table->string('provider', 20)->after('id');
            }

            if (! Schema::hasColumn('payments', 'provider_ref')) {
                $table->string('provider_ref', 150)->nullable()->after('provider');
            }

            // Kode menggunakan "meta" (JSON). Kita tambahkan "meta" tanpa menghapus "metadata".
            if (! Schema::hasColumn('payments', 'meta')) {
                // Letakkan setelah "status" agar rapi (tidak wajib)
                $table->json('meta')->nullable()->after('status');
            }
        });

        // Migrasi data: jika ada kolom "metadata" dan kolom "meta" masih kosong → salin nilainya
        if (Schema::hasColumn('payments', 'metadata') && Schema::hasColumn('payments', 'meta')) {
            // Portabel untuk SQLite/MySQL/PostgreSQL (JSON ke JSON)
            DB::statement('UPDATE payments SET meta = metadata WHERE meta IS NULL');
        }

        // Indeks & unik gabungan untuk akses cepat status/lookup
        Schema::table('payments', function (Blueprint $table) {
            // Index per kolom (opsional, tetap berguna)
            if (! self::indexExists('payments', 'payments_provider_index')) {
                $table->index('provider');
            }
            if (! self::indexExists('payments', 'payments_provider_ref_index')) {
                $table->index('provider_ref');
            }

            // Unik pasangan provider+provider_ref (jika keduanya ada)
            if (! self::indexExists('payments', 'payments_provider_provider_ref_unique')) {
                $table->unique(['provider', 'provider_ref'], 'payments_provider_provider_ref_unique');
            }
        });
    }

    public function down(): void
    {
        // Kembalikan data meta -> metadata bila metadata ada
        if (Schema::hasColumn('payments', 'metadata') && Schema::hasColumn('payments', 'meta')) {
            DB::statement('UPDATE payments SET metadata = meta WHERE metadata IS NULL');
        }

        Schema::table('payments', function (Blueprint $table) {
            // Hapus indeks unik & indeks biasa
            if (self::indexExists('payments', 'payments_provider_provider_ref_unique')) {
                $table->dropUnique('payments_provider_provider_ref_unique');
            }
            if (self::indexExists('payments', 'payments_provider_index')) {
                $table->dropIndex('payments_provider_index');
            }
            if (self::indexExists('payments', 'payments_provider_ref_index')) {
                $table->dropIndex('payments_provider_ref_index');
            }

            // Hapus kolom yang kita tambahkan
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
     * Helper sederhana cek keberadaan index berdasarkan nama.
     * Fungsi ini menggunakan informasi schema manager; aman untuk MySQL/SQLite/PG.
     */
    private static function indexExists(string $table, string $indexName): bool
    {
        try {
            $connection = Schema::getConnection();
            $schema = $connection->getDoctrineSchemaManager();
            $indexes = $schema->listTableIndexes($table);

            return array_key_exists($indexName, $indexes);
        } catch (\Throwable $e) {
            // Jika Doctrine tak tersedia, fallback "assume not exists"
            return false;
        }
    }
};
