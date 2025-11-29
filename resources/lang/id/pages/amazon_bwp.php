<?php

return [

    'hint' => 'x-amzn-signature pada setiap request.',

    'summary' => <<<'TEXT'
Buy with Prime (BWP) menandatangani setiap webhook sehingga kamu bisa memastikan benar-benar berasal dari Amazon dan tidak diubah selama transit. Setiap request menyertakan tanda tangan digital pada header `x-amzn-signature`. Handler kamu harus membangun kembali signature yang diharapkan secara persis seperti yang didokumentasikan BWP untuk tipe event dan environment yang digunakan; jika nilainya tidak cocok, tolak panggilan tersebut. Perlakukan setiap timestamp/nonce yang ikut dikirim sebagai bagian dari strategi anti-replay: terapkan jendela validitas yang ketat dan simpan identifier yang sudah diproses untuk menghindari duplikasi.

Secara operasional, rancang endpoint agar cepat dan deterministik: verifikasi lebih dulu, berikan ack `2xx` setelah aman tercatat, dan jalankan pekerjaan terberat secara asynchronous. Jika kamu mengandalkan allowlist, ingat bahwa IP dan jaringan bisa berubah—verifikasi kriptografis adalah jangkar kepercayaan utama. Simpan jejak audit yang aman (ID request, keberadaan signature, hasil verifikasi, dan hash dari body—bukan secret-nya). Untuk pengujian lokal, kamu bisa men-stub langkah verifikasi di balik flag environment sekaligus memastikan jalur produksi selalu memeriksa signature. Saat merotasi key atau memperbarui aturan kanonisasi, lakukan roll-forward dengan hati-hati, pantau tingkat error, dan dokumentasikan secara rinci set header serta hashing/kanonisasi yang kamu terapkan agar layanan lain di stack tetap sinkron.

Dari sisi ergonomi integrasi, tampilkan **alasan kegagalan yang jelas** (signature tidak valid, timestamp kedaluwarsa, request tidak valid) dan kembalikan error code yang stabil sehingga perilaku retry dapat diprediksi. Gabungkan ini dengan idempotensi di level aplikasi dan perlindungan replay agar transisi status pembayaran di downstream tetap aman bahkan ketika terjadi retry, lonjakan trafik, atau gangguan parsial.
TEXT
    ,

    // Dari view amazon_bwp.blade.php
    'docs' => 'https://documents.buywithprime.amazon.com/bwp-api/docs/subscribe-to-events',

    'signature_notes' => [
        'Baca `x-amzn-signature` dari request header.',
        'Bangun kembali signature yang diharapkan persis seperti yang didefinisikan Buy with Prime (algoritme/kanonisasi di dokumen resmi); tolak jika tidak cocok.',
        'Jika timestamp/nonce tersedia, terapkan jendela freshness yang ketat untuk mengurangi serangan replay; simpan ID yang sudah diproses untuk menghindari duplikasi.',
    ],

    'example_payload' => [
        'eventType' => 'ORDER_COMPLETED',
        'data' => [
            'orderId' => 'BWP-001',
            'status' => 'COMPLETED',
            'amount' => 25000,
            'currency' => 'IDR',
        ],
        'provider' => 'amazon_bwp',
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
            'path' => '/api/webhooks/amazon_bwp',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
