<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            /*
             * Primary key ULID (string).
             */
            $table->ulid('id')->primary();

            /*
             * Identitas payment di simulator:
             * - provider       : xendit|midtrans|stripe|...
             * - provider_ref   : "sim_xendit_..." (atau id yang kamu generate)
             *
             * Ini WAJIB untuk endpoint:
             * GET /api/v1/payments/{provider}/{provider_ref}/status
             * dan untuk update saat webhook masuk.
             */
            $table->string('provider', 32)->index();
            $table->string('provider_ref', 191);

            /*
             * Data payment
             */
            $table->unsignedInteger('amount');
            $table->string('currency', 10)->default('IDR');
            $table->string('description', 255)->nullable();

            /*
             * Metadata fleksibel.
             * Di layer request kamu bisa terima meta/metadata dan map ke sini.
             */
            $table->json('meta')->nullable();

            /*
             * Status payment (selaraskan dengan OpenAPI + domain):
             * pending | succeeded | failed
             */
            $table->string('status', 20)->default('pending')->index();

            /*
             * Idempotency-Key (opsional tapi recommended):
             * - nullable: supaya flow non-idempotent tetap bisa
             * - unique: satu key mengacu ke satu payment
             *
             * Catatan: MySQL mengizinkan banyak NULL dalam UNIQUE index. :contentReference[oaicite:2]{index=2}
             * SQLite juga memperlakukan NULL sebagai distinct untuk UNIQUE. :contentReference[oaicite:3]{index=3}
             */
            $table->string('idempotency_key', 255)->nullable();
            $table->unique('idempotency_key', 'payments_idempotency_key_unique');

            /*
             * (Opsional tapi sangat membantu) fingerprint payload untuk mendeteksi konflik idempotency:
             * Jika request datang dengan Idempotency-Key sama tapi body beda => bisa respon 409.
             * Implementasinya di service/controller, tapi kolom ini bikin konfliknya "tercatat" rapi.
             */
            $table->string('idempotency_request_hash', 64)->nullable()->index();

            $table->timestamps();

            /*
             * Lookup status harus cepat: provider + provider_ref.
             * Bisa unique (lebih aman) jika provider_ref memang unik per provider.
             */
            $table->unique(['provider', 'provider_ref'], 'payments_provider_provider_ref_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
