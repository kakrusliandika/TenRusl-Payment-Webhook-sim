<?php

return [

    'hint' => 'Signature callback khusus provider.',

    'summary' => <<<'TEXT'
Callback OY! adalah bagian dari postur keamanan yang lebih luas, yang dibangun di atas API key terdaftar dan allowlisting IP sumber untuk request partner. OY! juga menyediakan fitur Authorization Callback agar kamu bisa mengontrol dan menyetujui callback sebelum mencapai sistemmu, menambahkan gate yang eksplisit untuk mencegah perubahan status yang tidak diinginkan. Dalam praktiknya, tetap perlakukan setiap callback masuk sebagai tidak tepercaya sampai terverifikasi, terapkan freshness (jendela timestamp/nonce), dan buat consumer idempotent agar retry serta pengiriman out-of-order tetap aman.

Karena tiap provider publik berbeda dalam cara menandatangani callback, simulator ini mendemokan baseline yang lebih “hardened” menggunakan header HMAC (misalnya, `X-Callback-Signature`) yang dihitung di atas raw request body persis apa adanya dengan shared secret. Ini menggambarkan prinsip yang sama seperti di produksi: hashing berbasis raw bytes (tanpa re-serialisasi), perbandingan constant-time, dan jendela replay yang pendek. Padukan dengan dedup store kecil serta acknowledgement 2xx yang cepat supaya retry logic provider tetap sehat sambil menghindari side effect ganda.

Secara operasional, pertahankan audit trail (waktu diterima, hasil verifikasi, hash body—bukan secret), rotasi secret dengan aman, dan pantau tingkat kegagalan verifikasi. Jika kamu mengandalkan allowlist, ingat bahwa daftar tersebut dapat berubah; pemeriksaan kriptografis (atau gate otorisasi eksplisit dari OY!) harus tetap menjadi trust anchor utama. Jagalah endpoint tetap sempit, mudah diprediksi, dan terdokumentasi baik agar layanan lain dan rekan satu tim bisa menggunakannya ulang dengan percaya diri.
TEXT
    ,

    // dari view OY
    'docs' => 'https://docs.oyindonesia.com/',

    'signature_notes' => [
        'Gunakan postur keamanan OY!: API key terdaftar + allowlisting IP sumber untuk request partner.',
        'Manfaatkan Authorization Callback (dashboard) untuk menyetujui callback sebelum masuk ke sistemmu.',
        'Di simulator ini, verifikasi `X-Callback-Signature = HMAC-SHA256(raw_body, secret)` sebagai model best-practice; terapkan constant-time compare & pengecekan freshness.',
        'Buat proses idempotent dan kembalikan 2xx dengan cepat agar retry provider tetap sehat.',
    ],

    'example_payload' => [
        'id' => 'evt_'.now()->timestamp,
        'type' => 'payment.completed',
        'provider' => 'oy',
        'data' => [
            'partner_trx_id' => 'PRT-001',
            'amount' => 25000,
            'currency' => 'IDR',
            'status' => 'COMPLETED',
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
            'path' => '/api/webhooks/oy',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
