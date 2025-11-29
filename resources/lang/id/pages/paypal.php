<?php

return [

    'hint' => 'Verify Webhook Signature API.',

    'summary' => <<<'TEXT'
PayPal mewajibkan verifikasi server-side untuk setiap webhook melalui Verify Webhook Signature API resmi. Listener kamu harus mengekstrak header yang dikirim bersama notifikasi—`PAYPAL-TRANSMISSION-ID`, `PAYPAL-TRANSMISSION-TIME`, `PAYPAL-CERT-URL`, dan `PAYPAL-TRANSMISSION-SIG`—bersama `webhook_id` milikmu dan **raw** request body (`webhook_event`). Kirim semua nilai tersebut ke endpoint verifikasi, dan terima event hanya jika PayPal mengembalikan hasil sukses. Ini menggantikan mekanisme verifikasi yang lebih lama dan memudahkan konsistensi di seluruh produk REST.

Bangun consumer sebagai gerbang yang cepat dan idempoten: verifikasi dulu, simpan event record, ACK dengan 2xx, lalu dorong pekerjaan berat ke antrian. Gunakan perbandingan constant-time untuk pengecekan lokal apa pun dan pertahankan raw bytes saat meneruskan ke PayPal agar tidak kena bug halus akibat re-serialisasi. Terapkan toleransi waktu yang ketat di sekitar `PAYPAL-TRANSMISSION-TIME` untuk mengecilkan replay window, dan log audit seminimal mungkin (request ID, hasil verifikasi, hash body—tanpa secret). Dengan pola ini, pengiriman duplikat dan outage parsial tidak akan menyebabkan pemrosesan ganda, dan audit trail tetap tepercaya saat incident response.
TEXT
    ,

    // Dari view PayPal
    'docs' => 'https://developer.paypal.com/docs/api/webhooks/v1/',

    'signature_notes' => [
        'Kumpulkan header: PAYPAL-TRANSMISSION-ID, PAYPAL-TRANSMISSION-TIME, PAYPAL-CERT-URL, PAYPAL-TRANSMISSION-SIG; simpan raw body.',
        'Panggil Verify Webhook Signature API dengan nilai tersebut plus webhook_id dan webhook_event; terima hanya jika sukses.',
        'Jadikan verifikasi sebagai gate; terapkan toleransi waktu pendek untuk mitigasi replay dan buat consumer idempoten.',
        'Balas 2xx dengan cepat, antrikan pekerjaan berat, dan log diagnostik minimal (tanpa secret).',
    ],

    'example_payload' => [
        'id' => 'WH-'.now()->timestamp,
        'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
        'resource' => [
            'id' => 'CAP-001',
            'status' => 'COMPLETED',
        ],
        'provider' => 'paypal',
        'create_time' => now()->toIso8601String(),
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
            'path' => '/api/webhooks/paypal',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
