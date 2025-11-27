<?php

return [

    'hint' => 'Notifikasi khusus produk.',

    'summary' => <<<'TEXT'
Payoneer Checkout mengirim notifikasi asinkron (webhook) ke endpoint yang kamu kontrol, supaya sistemmu bisa merekonsiliasi status pembayaran dengan aman di luar browser pengguna. Platform ini memungkinkan kamu menetapkan notification URL khusus dan memilih gaya pengiriman yang paling cocok untuk stack-mu—POST (direkomendasikan) atau GET, dengan JSON atau parameter form-encoded. Karena set parameter serta pola penandatanganan/autentikasi bergantung pada produk, perlakukan notifikasi Payoneer sebagai permukaan integrasi: dokumentasikan header/field yang mengidentifikasi event, sertakan metadata anti-replay jika tersedia, dan verifikasi autentisitas sebelum mengubah state.

Secara operasional, mulai dengan handler yang sempit dan idempoten: simpan catatan event yang immutable lalu kembalikan 2xx dengan cepat. Simpan logika bisnis yang berat di background worker agar tidak memicu retry storm. Terapkan kunci deduplikasi dan enforce jendela freshness pada timestamp/nonce untuk melindungi dari replay atau pengiriman out-of-order. Jika butuh jaminan tambahan, tambahkan token dari provider (atau secret acak milikmu) pada notification URL dan validasi di sisi server. Terakhir, buat runbook untuk tim yang mendokumentasikan endpoint, format, kode kegagalan, dan langkah verifikasi yang kamu terapkan untuk varian produk Payoneer-mu—serta versioning-kan bersama kode.
TEXT
    ,

    // Dari view Payoneer
    'docs' => 'https://checkoutdocs.payoneer.com/docs/create-notification-endpoints',

    'signature_notes' => [
        'Sediakan endpoint notifikasi khusus (POST direkomendasikan); terima JSON atau data form.',
        'Validasi autentisitas sesuai dokumentasi varian produkmu (token atau field signature); tolak jika tidak cocok.',
        'Terapkan freshness timestamp/nonce saat tersedia dan buat proses idempoten (simpan dedup key).',
        'ACK cepat (2xx) dan offload pekerjaan berat ke background jobs; simpan audit trail tanpa logging secret.',
    ],

    'example_payload' => [
        'event'     => 'checkout.transaction.completed',
        'provider'  => 'payoneer',
        'data'      => [
            'orderId'  => 'PO-001',
            'amount'   => 25000,
            'currency' => 'IDR',
            'status'   => 'COMPLETED',
        ],
        'sent_at'   => now()->toIso8601String(),
    ],

    'endpoints' => [
        [
            'method' => 'POST',
            'path'   => '/api/payments',
            'desc'   => __('pages.create_payment'),
        ],
        [
            'method' => 'GET',
            'path'   => '/api/payments/{id}',
            'desc'   => __('pages.get_payment'),
        ],
        [
            'method' => 'POST',
            'path'   => '/api/webhooks/payoneer',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
