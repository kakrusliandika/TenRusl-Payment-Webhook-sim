<?php

return [

    'hint' => 'Validasi signature_key.',

    'summary' => <<<'TEXT'
Midtrans menyertakan `signature_key` yang dihitung di setiap notifikasi HTTP(S) agar kamu bisa memverifikasi asal notifikasi sebelum memprosesnya. Rumusnya jelas dan stabil:
    SHA512(order_id + status_code + gross_amount + ServerKey)
Bangun string input menggunakan nilai persis dari body notifikasi (sebagai string) dan `ServerKey` privat milikmu, lalu hitung SHA-512 hex digest dan bandingkan dengan `signature_key` menggunakan perbandingan constant-time. Jika verifikasi gagal, abaikan notifikasi. Untuk pesan yang valid, gunakan field yang terdokumentasi (misalnya `transaction_status`) untuk menggerakkan state machine—ACK cepat (2xx), enqueue pekerjaan berat, dan buat pembaruan idempotent untuk mengantisipasi retry atau pengiriman yang tidak berurutan.

Dua jebakan umum: formatting dan coercion. Pertahankan `gross_amount` persis seperti yang diberikan (jangan dilokalkan, jangan ubah desimal) saat menyusun string, dan hindari trimming atau perubahan newline/whitespace. Simpan deduplication key per-event atau per-order untuk melindungi dari race condition; log hasil verifikasi dan hash body untuk audit tanpa membocorkan secret. Padukan dengan rate limiting pada endpoint dan kode kegagalan yang jelas agar monitoring bisa membedakan error sementara (boleh retry) vs penolakan permanen (signature tidak valid).
TEXT
    ,

    // dari view midtrans
    'docs' => 'https://docs.midtrans.com/docs/https-notification-webhooks',

    'signature_notes' => [
        'Ambil `order_id`, `status_code`, `gross_amount` dari body (sebagai string) lalu tambahkan `ServerKey` milikmu.',
        'Hitung `SHA512(order_id + status_code + gross_amount + ServerKey)` dan bandingkan dengan `signature_key` (constant-time).',
        'Tolak jika tidak cocok; jika cocok, update state dari `transaction_status`. Pastikan idempotent & kembalikan 2xx dengan cepat.',
        'Waspadai perubahan format `gross_amount` dan whitespace tak sengaja saat menggabungkan string.',
    ],

    'example_payload' => [
        'order_id'           => 'ORDER-001',
        'status_code'        => '200',
        'gross_amount'       => '25000.00',
        'transaction_status' => 'settlement',
        'signature_key'      => '<sha512>',
        'provider'           => 'midtrans',
        'sent_at'            => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/midtrans',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
