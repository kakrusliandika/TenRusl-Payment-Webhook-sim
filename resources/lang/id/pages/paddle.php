<?php

return [

    'hint' => 'Tanda tangan public-key (Classic) / secret (Billing).',

    'summary' => <<<'TEXT'
Paddle Billing menandatangani setiap webhook dengan header `Paddle-Signature` yang memuat Unix timestamp (`ts`) dan signature (`h1`). Untuk verifikasi manual, gabungkan timestamp, tanda titik dua, dan raw request body persis apa adanya untuk membentuk signed payload; lalu hitung HMAC-SHA256 menggunakan secret milik notification destination milikmu, dan bandingkan hasilnya dengan `h1` menggunakan fungsi constant-time. Paddle membuat secret terpisah untuk tiap notification destination—perlakukan seperti password dan jangan pernah simpan di source control.

Gunakan SDK resmi atau middleware verifikasi milikmu untuk memverifikasi sebelum melakukan parsing apa pun. Karena timing dan transformasi body sering jadi jebakan, pastikan framework-mu bisa mengakses raw bytes (misalnya Express `express.raw({ type: 'application/json' })`) dan terapkan toleransi waktu yang pendek pada `ts` untuk mencegah replay. Setelah verifikasi sukses, ACK cepat (2xx), simpan event ID untuk idempotensi, dan pindahkan pekerjaan berat ke background jobs. Ini menjaga delivery tetap andal dan mencegah side effect ganda saat terjadi retry.

Saat migrasi dari Paddle Classic, perhatikan bahwa verifikasi bergeser dari signature berbasis public-key ke HMAC berbasis secret untuk Billing. Perbarui runbook dan manajemen secret, lalu pantau metrik verifikasi saat rollout perubahan. Log yang jelas (tanpa secret) dan respons error yang deterministik sangat membantu saat incident handling dan dukungan partner.
TEXT
    ,

    // Dari view Paddle
    'docs' => 'https://developer.paddle.com/webhooks/signature-verification',

    'signature_notes' => [
        'Baca header `Paddle-Signature`; parse nilai `ts` dan `h1`.',
        'Buat signed payload = `ts + ":" + <raw request body>`; hash dengan secret endpoint milikmu.',
        'Bandingkan hash milikmu dengan `h1` menggunakan fungsi timing-safe; terapkan toleransi waktu pendek pada `ts` untuk mencegah replay.',
        'Utamakan SDK resmi atau middleware verifikasi; parsing JSON hanya setelah verifikasi berhasil.',
    ],

    'example_payload' => [
        'event_id' => 'evt_'.now()->timestamp,
        'event_type' => 'transaction.completed',
        'provider' => 'paddle',
        'data' => [
            'transaction_id' => 'txn_001',
            'amount' => 25000,
            'currency_code' => 'IDR',
            'status' => 'completed',
        ],
        'occurred_at' => now()->toIso8601String(),
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
            'path' => '/api/webhooks/paddle',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
