<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            // ULID primary key (string)
            $table->ulid('id')->primary();

            $table->unsignedInteger('amount');
            $table->string('currency', 10)->default('IDR');
            $table->string('description', 140)->nullable();
            $table->json('metadata')->nullable();

            // pending | paid | failed | refunded
            $table->string('status', 20)->default('pending')->index();

            /**
             * Idempotency-Key unik per intent create.
             *
             * - nullable: supaya payment yang tidak dibuat via endpoint idempotent
             *   tetap bisa dibuat tanpa harus punya key.
             * - unique: satu key hanya boleh mengacu ke satu payment.
             *
             * Catatan: di MySQL, UNIQUE + nullable masih mengizinkan banyak NULL.
             */
            $table->string('idempotency_key', 100)
                ->nullable();

            $table->unique('idempotency_key', 'payments_idempotency_key_unique');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
