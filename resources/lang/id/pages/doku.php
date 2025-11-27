<?php

return [

    'hint' => 'Signature dengan header Client-Id/Request-*.',

    'summary' => <<<'TEXT'
DOKU mengamankan HTTP Notification dengan signature kanonis berbasis header yang wajib kamu verifikasi sebelum memproses payload apa pun. Setiap callback datang dengan header `Signature` yang nilainya berbentuk `HMACSHA256=<base64>`. Untuk membangun ulang nilai yang diharapkan, pertama-tama hitung `Digest` untuk request body: SHA-256 dari bytes JSON mentah, lalu di-base64-encode. Berikutnya, susun string yang dipisahkan newline, terdiri dari lima komponen dengan urutan dan penulisan persis seperti ini:
  • `Client-Id:<value>`
  • `Request-Id:<value>`
  • `Request-Timestamp:<value>`
  • `Request-Target:<path-of-your-notification-url>` (mis. `/payments/notifications`)
  • `Digest:<base64-of-SHA256(body)>`
Lalu hitung HMAC menggunakan SHA-256 dengan DOKU Secret Key sebagai key atas string kanonis tersebut, base64-encode hasilnya, dan tambahkan prefix `HMACSHA256=`. Terakhir, bandingkan dengan header `Signature` menggunakan perbandingan constant-time. Setiap mismatch, komponen yang hilang, atau nilai yang tidak valid harus diperlakukan sebagai kegagalan autentikasi dan request harus ditolak segera.

Agar tahan banting dan aman, ACK notifikasi yang valid dengan cepat (2xx) dan dorong pekerjaan berat ke background job supaya tidak memicu retry. Buat consumer idempotent dengan mencatat identifier yang sudah diproses (mis. `Request-Id` atau event ID di body). Validasi freshness: `Request-Timestamp` harus berada dalam window yang ketat untuk mencegah replay attack; pastikan juga `Request-Target` sesuai dengan route yang sebenarnya agar terhindar dari bug canonicalization. Saat parsing, ikuti panduan DOKU untuk tidak terlalu ketat: abaikan field yang tidak dikenal dan prioritaskan evolusi skema dibanding parser yang rapuh. Saat incident response, log keberadaan header wajib, digest/signature yang dihitung (jangan pernah log secret), dan hash dari body untuk membantu audit tanpa membocorkan data sensitif.
TEXT
    ,

    // dari view DOKU
    'docs' => 'https://developers.doku.com/get-started-with-doku-api/signature-component/non-snap/signature-component-from-request-header',

    'signature_notes' => [
        'Baca header: `Client-Id`, `Request-Id`, `Request-Timestamp`, `Signature`, dan infer `Request-Target` (path route kamu).',
        'Hitung `Digest = base64( SHA256(raw JSON body) )`.',
        'Bangun string kanonis dengan baris: Client-Id, Request-Id, Request-Timestamp, Request-Target, Digest (urutan tersebut, masing-masing satu baris, tanpa newline di akhir).',
        'Hitung expected = `HMACSHA256=base64( HMAC-SHA256(canonical, SecretKey) )`; bandingkan dengan `Signature` menggunakan constant-time.',
        'Terapkan freshness timestamp; buat pemrosesan idempotent; ACK cepat (2xx) dan offload pekerjaan berat.',
    ],

    'example_payload' => [
        'order'       => ['invoice_number' => 'INV-001'],
        'transaction' => ['status' => 'SUCCESS'],
        'provider'    => 'doku',
        'sent_at'     => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/doku',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
