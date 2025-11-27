<?php

return [

    'hint' => 'Tanda tangan callback gaya MD5/HMAC.',

    'summary' => <<<'TEXT'
Skrill mengirim status callback ke `status_url` milikmu dan mengharuskan kamu memvalidasi pesan memakai `md5sig`, yaitu **MD5 huruf besar (uppercase)** dari concatenation field yang sudah ditentukan (contoh: `merchant_id + transaction_id + UPPERCASE(MD5(secret_word)) + mb_amount + mb_currency + status`). Payload baru boleh dipercaya jika nilai yang kamu hitung cocok dengan `md5sig` yang diterima. Skrill juga mendukung opsi alternatif `sha2sig` (SHA-2 uppercase) atas permintaan, yang dibangun dengan pola yang serupa dengan `md5sig`.

Dalam praktik, lakukan validasi signature di backend saja (jangan pernah mengekspos secret word), dan hash **nilai parameter persis** seperti yang diposting kembali ke endpoint kamu. Buat endpoint idempoten (dedup berdasarkan transaction atau event ID), balas 2xx cepat setelah persist, dan tunda pekerjaan yang tidak kritikal. Saat debugging, log hasil verifikasi dan hash body tanpa memasukkan secret ke log. Perhatikan format—field amount dan currency harus dipakai verbatim saat membangun string signature—agar perbandingan stabil di berbagai retry dan environment.
TEXT
    ,

    // dari view Skrill
    'docs' => 'https://www.skrill.com/fileadmin/content/pdf/Skrill_Quick_Checkout_Guide_v10.3.pdf',

    'signature_notes' => [
        'Bangun ulang `md5sig` persis: gabungkan field yang didokumentasikan (mis. merchant_id, transaction_id, UPPERCASE(MD5(secret_word)), mb_amount, mb_currency, status) lalu hitung **MD5 uppercase**.',
        'Bandingkan dengan `md5sig` yang diterima; opsional gunakan `sha2sig` (SHA-2 uppercase) jika diaktifkan oleh Skrill.',
        'Validasi hanya di server dengan nilai yang diposting apa adanya; buat handler idempoten & kembalikan 2xx dengan cepat.',
    ],

    'example_payload' => [
        'transaction_id' => 'SKR-001',
        'mb_amount'      => '10.00',
        'mb_currency'    => 'EUR',
        'status'         => '2',
        'md5sig'         => '<UPPERCASE_MD5>',
        'provider'       => 'skrill',
        'sent_at'        => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/skrill',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
