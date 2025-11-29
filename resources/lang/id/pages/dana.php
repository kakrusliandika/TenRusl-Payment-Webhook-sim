<?php

return [

    'hint' => 'RSA (verifikasi dengan public key DANA).',

    'summary' => <<<'TEXT'
DANA menggunakan skema tanda tangan **asimetris**: request ditandatangani dengan private key, dan integrator memverifikasinya menggunakan **public key resmi DANA**. Secara praktik, kamu mengambil signature dari header webhook (misalnya `X-SIGNATURE`), melakukan base64-decode, lalu memverifikasi raw HTTP request body terhadap signature tersebut menggunakan RSA-2048 dengan SHA-256. Hanya jika verifikasi mengembalikan hasil positif, payload boleh dianggap autentik. Jika verifikasi gagal—atau signature/header tidak ada—balas dengan kode non-2xx dan hentikan pemrosesan.

Karena webhook bisa di-retry atau dikirim tidak berurutan, rancang handler agar idempotent: simpan event identifier yang unik dan hentikan pemrosesan duplikat; validasi timestamp/nonce untuk memastikan freshness guna mengurangi replay; dan anggap semua field tidak tepercaya sampai verifikasi signature berhasil. Hindari re-serializing JSON sebelum verifikasi; lakukan hash/verify persis pada bytes yang diterima lewat jaringan. Jangan taruh secret dan private key di log; jika perlu logging, catat hanya diagnostik tingkat tinggi (hasil verifikasi, hash body, event ID) dan amankan log tersebut saat tersimpan.

Untuk tim, publikasi runbook singkat yang mencakup: cara memuat atau merotasi public key DANA, cara verifikasi di setiap bahasa/runtime yang kamu pakai, aturan string-to-sign yang tepat untuk integrasimu, dan apa yang termasuk kegagalan permanen vs sementara. Padukan ini dengan kebijakan retry/backoff yang kuat, work queue yang dibatasi, health check, dan alert saat verifikasi gagal. Hasilnya adalah konsumer webhook yang aman saat beban tinggi, tahan terhadap retry, dan sesuai dengan verifikasi kriptografis yang DANA wajibkan.
TEXT
    ,

    // dari view DANA
    'docs' => 'https://dashboard.dana.id/api-docs-v2/guide/authentication',

    'signature_notes' => [
        'Base64-decode nilai dari header `X-SIGNATURE`.',
        'Verifikasi RSA-2048 + SHA-256 atas raw HTTP body yang persis sama menggunakan public key resmi DANA; terima hanya jika verifikasi bernilai positif.',
        'Tolak webhook apa pun yang signature-nya hilang/tidak valid atau payload-nya tidak benar; jangan percaya data sebelum verifikasi sukses.',
    ],

    'example_payload' => [
        'id' => 'evt_'.now()->timestamp,
        'type' => 'payment.paid',
        'provider' => 'dana',
        'data' => [
            'transaction_id' => 'DANA-001',
            'amount' => 25000,
            'currency' => 'IDR',
            'status' => 'SUCCESS',
        ],
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
            'path' => '/api/webhooks/dana',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
