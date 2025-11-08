<?php

namespace App\Services\Payments\Contracts;

/**
 * Kontrak adapter pembayaran eksternal (simulasi).
 * Implementasi: MockAdapter, XenditAdapter, MidtransAdapter (stub).
 */
interface PaymentAdapter
{
    /**
     * Simulasikan pembuatan "charge" di provider eksternal.
     * Mengembalikan array standar (tidak mengubah DB internal).
     *
     * @param array $payload  amount, currency, description, metadata
     * @return array{provider:string, reference:string, status:string, raw:array}
     */
    public function createCharge(array $payload): array;

    /**
     * Simulasikan cek status transaksi di provider.
     *
     * @param string $reference
     * @return array{provider:string, reference:string, status:string, raw:array}
     */
    public function fetchStatus(string $reference): array;
}
