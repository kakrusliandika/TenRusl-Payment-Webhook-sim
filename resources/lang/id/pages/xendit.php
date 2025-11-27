<?php

return [

    'hint' => 'Signature token callback.',

    'summary' => <<<'TEXT'
Xendit menandatangani event webhook menggunakan token per-akun yang dikirim melalui header `x-callback-token`. Integrasi kamu harus membandingkan header ini dengan token yang kamu ambil dari dashboard Xendit, lalu menolak request yang tokennya tidak ada atau tidak cocok. Sebagian produk webhook juga menyertakan `webhook-id` yang bisa kamu simpan untuk mencegah pemrosesan ganda saat terjadi retry.

Secara operasional, jadikan verifikasi sebagai langkah pertama, simpan event record yang immutable, akui dengan 2xx secepatnya, dan pindahkan pekerjaan berat ke queue. Terapkan idempotensi menggunakan `webhook-id` (atau key kamu sendiri) dan gunakan time window yang ketat jika tersedia metadata timestamp. Dokumentasikan alur lengkap (verifikasi, deduplikasi, retry, dan error code) supaya tim dan service lain bisa terintegrasi konsisten di semua environment.
TEXT
    ,

    // dari view Xendit
    'docs' => 'https://docs.xendit.co/docs/handling-webhooks',

    'signature_notes' => [
        'Bandingkan `x-callback-token` dengan token unik dari dashboard Xendit; tolak jika mismatch.',
        'Gunakan `webhook-id` (jika ada) untuk deduplikasi; anggap verifikasi sebagai gate keras sebelum parsing JSON.',
        'Respon 2xx cepat dan tunda pekerjaan berat; log diagnostik minimal tanpa membocorkan secret.',
    ],

    'example_payload' => [
        'id'       => 'evt_xnd_' . now()->timestamp,
        'event'    => 'invoice.paid',
        'data'     => [
            'id'     => 'inv_001',
            'status' => 'PAID',
        ],
        'provider' => 'xendit',
        'sent_at'  => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/xendit',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
