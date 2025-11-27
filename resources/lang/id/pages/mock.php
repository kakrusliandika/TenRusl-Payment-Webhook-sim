<?php

return [

    'hint' => 'Tes cepat.',

    'summary' => <<<'TEXT'
Provider mock ini adalah playground yang deterministik dan tanpa kredensial untuk melatih seluruh siklus hidup webhook: pembuatan request, transisi status yang idempotent, pengiriman, verifikasi, retry, dan penanganan kegagalan. Karena berjalan tanpa dependensi eksternal, kamu bisa iterasi secara lokal atau di CI, merekam fixtures, dan mendemokan keputusan arsitektur (misalnya menaruh verifikasi di mana vs. persistensi) tanpa membocorkan secret sungguhan.

Gunakan untuk mensimulasikan mode kegagalan umum: pengiriman terlambat, pengiriman duplikat, event tidak berurutan, serta respons 5xx sementara yang memicu exponential backoff. Mock ini juga mendukung berbagai “mode signature” (none / HMAC-SHA256 / RSA-verify stub) agar tim bisa latihan raw-body hashing, perbandingan constant-time, dan timestamp window dalam lingkungan yang aman. Ini membantu kamu memvalidasi idempotency keys dan tabel deduplikasi sebelum integrasi ke payment gateway nyata.

Untuk kualitas dokumentasi, buat mock sedekat mungkin dengan produksi: bentuk endpoint, header, dan error code sama; bedanya hanya pada trust root. ACK webhook yang valid dengan cepat (2xx) dan offload pekerjaan berat ke background job. Anggap payload mock sebagai tidak tepercaya sampai verifikasi lolos—baru terapkan aturan bisnis. Hasilnya adalah feedback loop yang cepat dan demo yang portable yang mencerminkan arsitektur yang benar-benar akan kamu rilis.
TEXT
    ,

    // dari view mock
    'docs' => null,

    'signature_notes' => [
        'Mode simulator: none / HMAC-SHA256 / RSA-verify stub; pilih via config untuk latihan jalur verifikasi.',
        'Hash raw request body yang persis sama; bandingkan dengan fungsi timing-safe; terapkan jendela replay yang pendek.',
        'Catat event ID yang sudah diproses untuk idempotensi; ACK webhook valid cepat (2xx) dan tunda/offload pekerjaan berat.',
    ],

    'example_payload' => [
        'id'       => 'evt_' . now()->timestamp,
        'type'     => 'payment.succeeded',
        'provider' => 'mock',
        'data'     => [
            'payment_id' => 'pay_mock_001',
            'amount'     => 25000,
            'currency'   => 'IDR',
            'status'     => 'succeeded',
        ],
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
            'path'   => '/api/webhooks/mock',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
