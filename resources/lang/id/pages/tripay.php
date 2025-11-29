<?php

return [

    'hint' => 'X-Callback-Signature.',

    'summary' => <<<'TEXT'
TriPay mengirim callback ke URL yang kamu konfigurasi dan menyertakan header yang mengidentifikasi event serta membantu mengautentikasi pengirim. Secara khusus, callback membawa `X-Callback-Event` dengan nilai seperti `payment_status`, dan `X-Callback-Signature` untuk validasi signature sesuai dokumentasi TriPay. Consumer kamu harus membaca header ini, memverifikasi keaslian request, lalu barulah memperbarui state internal.

Rancang endpoint agar cepat dan idempoten. Gunakan freshness window yang singkat jika ada timestamp/nonce, dan pertahankan penyimpanan deduplikasi yang ringan berbasis reference atau event identifier. Kembalikan 2xx dengan cepat setelah event tercatat, lalu tangani side effects secara asinkron. Untuk transparansi dan penanganan insiden, simpan audit trail yang mencatat waktu penerimaan, metadata event, dan hasil verifikasi tanpa melog secret.
TEXT
    ,

    // dari view Tripay
    'docs' => 'https://tripay.co.id/developer',

    'signature_notes' => [
        'Periksa `X-Callback-Event` (mis., `payment_status`) dan `X-Callback-Signature`.',
        'Validasi signature sesuai dokumentasi TriPay; tolak jika mismatch atau header tidak ada.',
        'Buat proses idempoten (dedup berdasarkan reference/event ID) dan akui cepat (2xx).',
    ],

    'example_payload' => [
        'reference' => 'TRX-001',
        'status' => 'PAID',
        'amount' => 125000,
        'currency' => 'IDR',
        'provider' => 'tripay',
        'sent_at' => now()->toIso8601String(),
    ],

    'endpoints' => [
        [
            'method' => 'POST',
            'path' => '/api/payments',
            'desc' => __('pages.create_payment'),
        ],
        [
            'method' => 'GET',
            'path' => '/api/payments/{id}',
            'desc' => __('pages.get_payment'),
        ],
        [
            'method' => 'POST',
            'path' => '/api/webhooks/tripay',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
