<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
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

            // Idempotency-Key unik per intent create
            $table->string('idempotency_key', 100)->unique();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
