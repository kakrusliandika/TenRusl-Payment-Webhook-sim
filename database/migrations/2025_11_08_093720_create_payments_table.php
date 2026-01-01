<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table): void {
            /*
             * Primary key ULID (string).
             */
            $table->ulid('id')->primary();

            /*
             * Identitas payment di simulator:
             * - provider     : xendit|midtrans|stripe|...
             * - provider_ref : "sim_xendit_..." (atau id yang kamu generate)
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
             */
            $table->json('meta')->nullable();

            /*
             * Status payment:
             * pending | succeeded | failed
             */
            $table->string('status', 20)->default('pending')->index();

            /*
             * Idempotency-Key
             */
            $table->string('idempotency_key', 255)->nullable();
            $table->unique('idempotency_key', 'payments_idempotency_key_unique');

            /*
             * Fingerprint request untuk deteksi konflik idempotency (409).
             */
            $table->string('idempotency_request_hash', 64)->nullable()->index();

            $table->timestamps();

            /*
             * Lookup status cepat: provider + provider_ref.
             */
            $table->unique(['provider', 'provider_ref'], 'payments_provider_provider_ref_unique');

            /*
             * Index tambahan untuk Admin List.
             */
            $table->index(['status', 'created_at'], 'payments_status_created_at_idx');
            $table->index(['provider', 'created_at'], 'payments_provider_created_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
