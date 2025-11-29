<?php

declare(strict_types=1);

namespace App\Services\Payments\Contracts;

/**
 * Kontrak umum untuk adapter pembayaran di simulator.
 *
 * Setiap adapter harus:
 * - Mengembalikan nama provider (harus selaras dengan allowlist & route param).
 * - Menyediakan operasi create() (membuat referensi pembayaran simulasi).
 * - Menyediakan operasi status() (mengambil status berdasarkan provider_ref).
 */
interface PaymentAdapter
{
    /**
     * Nama provider, contoh: "mock", "xendit", "midtrans", "stripe", dll.
     */
    public function provider(): string;

    /**
     * Buat pembayaran simulasi.
     *
     * @param  array  $input  bebas (amount/currency/description/metadata, dsb.)
     * @return array{provider:string, provider_ref:string, status:string, snapshot:array}
     */
    public function create(array $input): array;

    /**
     * Ambil status simulasi berdasarkan provider_ref.
     *
     * @return array{provider:string, provider_ref:string, status:string}
     */
    public function status(string $providerRef): array;
}
