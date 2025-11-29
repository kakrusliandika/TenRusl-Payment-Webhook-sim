<?php

return [

    'hint' => 'HMAC-SHA256 atas x-timestamp + body.',

    'summary' => <<<'TEXT'
Webhook Airwallex ditandatangani sehingga kamu bisa memverifikasi keaslian (authenticity) dan integritasnya sebelum menyentuh database. Setiap request menyertakan dua header penting: `x-timestamp` dan `x-signature`. Untuk memvalidasi pesan, baca body HTTP mentah persis seperti yang diterima, gabungkan nilai `x-timestamp` (sebagai string) dengan body mentah tersebut untuk membentuk input digest, lalu hitung HMAC menggunakan SHA-256 dengan secret bersama (shared secret) dari URL notifikasi kamu sebagai key. Airwallex mengharapkan hasilnya sebagai **hex digest**; bandingkan nilai tersebut dengan header `x-signature` menggunakan perbandingan dengan waktu konstan (constant-time comparison) untuk menghindari kebocoran timing. Jika signature tidak cocok, atau timestamp hilang/tidak valid, tutup rapat (fail closed) dan kembalikan respons non-2xx.

Karena replay adalah risiko nyata di sistem webhook mana pun, terapkan jendela “freshness” pada `x-timestamp`. Tolak pesan yang terlalu lama atau jauh di masa depan, dan simpan ID event yang sudah diproses untuk mendeduplikasi efek samping di hilir (idempotensi di lapisan aplikasi). Perlakukan payload sebagai data yang tidak tepercaya sampai verifikasi lulus; jangan stringify JSON lagi sebelum hashing—gunakan bytes mentah persis seperti saat diterima untuk menghindari perbedaan halus pada spasi/urutan. Ketika verifikasi berhasil, segera kembalikan respons `2xx`; kerjakan proses berat secara asynchronous agar logika retry tetap sehat dan mengurangi duplikasi tidak sengaja.

Untuk alur lokal dan CI, Airwallex menyediakan tooling kelas satu: konfigurasikan URL notifikasi di dashboard, pratinjau payload contoh, dan **kirim event uji** ke endpoint kamu. Saat debugging, log `x-timestamp` yang diterima, pratinjau signature yang dihitung (jangan pernah log secret), dan setiap ID event bila tersedia. Jika kamu melakukan rotasi kunci secret, lakukan dengan hati-hati dan pantau tingkat error signature. Terakhir, dokumentasikan seluruh rantai—verifikasi, deduplikasi, retry, dan respons error—agar rekan satu tim dapat mereproduksi hasil dengan aturan hashing raw body dan jendela waktu yang sama.
TEXT
    ,

    'docs' => 'https://www.airwallex.com/docs/developer-tools/webhooks/listen-for-webhook-events',

    'signature_notes' => [
        'Ambil `x-timestamp` dan `x-signature` dari header.',
        'Bangun value_to_digest = <x-timestamp> + <raw HTTP body> (byte persis).',
        'Hitung expected = HMAC-SHA256(value_to_digest, <webhook secret>) dalam bentuk HEX; bandingkan dengan `x-signature` menggunakan perbandingan waktu konstan.',
        'Tolak jika signature tidak cocok atau timestamp sudah kedaluwarsa; juga deduplikasi ID event yang sudah diproses untuk menjaga idempotensi.',
    ],

    'example_payload' => [
        'id' => 'evt_'.now()->timestamp,
        'type' => 'payment_intent.succeeded',
        'data' => [
            'payment_intent_id' => 'pi_awx_001',
            'amount' => 25000,
            'currency' => 'IDR',
            'status' => 'succeeded',
        ],
        'provider' => 'airwallex',
        'created_at' => now()->toIso8601String(),
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
            'path' => '/api/webhooks/airwallex',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
