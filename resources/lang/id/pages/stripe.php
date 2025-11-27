<?php

return [

    'hint' => 'Header signature dengan timestamp.',

    'summary' => <<<'TEXT'
Stripe menandatangani setiap request webhook dan menaruh signature yang dihitung di header `Stripe-Signature`. Endpoint kamu wajib memverifikasi request sebelum melakukan pekerjaan apa pun. Dengan library resmi Stripe, masukkan tiga input ke rutinitas verifikasi: raw request body yang persis apa adanya, header `Stripe-Signature`, dan endpoint secret milikmu. Lanjutkan hanya jika verifikasi sukses; jika tidak, kembalikan non-2xx dan hentikan pemrosesan. Saat tidak bisa memakai library resmi, lakukan verifikasi manual sesuai dokumentasi, termasuk pengecekan toleransi timestamp untuk mengurangi risiko replay.

Anggap verifikasi signature sebagai gate yang ketat. Buat handler idempoten (simpan event ID), balas 2xx cepat setelah persist, dan dorong pekerjaan berat ke background jobs. Pastikan framework kamu menyediakan **raw bytes**—hindari re-serialize JSON sebelum hashing, karena perubahan whitespace atau urutan field akan membuat signature check gagal. Terakhir, log diagnostik seminimal mungkin (hasil verifikasi, tipe event, hash body—tanpa secret) dan pantau kegagalan saat rotasi secret atau perubahan endpoint.
TEXT
    ,

    // dari view Stripe
    'docs' => 'https://docs.stripe.com/webhooks',

    'signature_notes' => [
        'Baca header `Stripe-Signature`; ambil endpoint secret dari Stripe dashboard.',
        'Verifikasi dengan library resmi dengan input: raw request body, `Stripe-Signature`, dan endpoint secret.',
        'Jika verifikasi manual, terapkan toleransi timestamp untuk mengurangi replay, dan bandingkan signature dengan fungsi timing-safe.',
        'Terima hanya jika sukses; simpan event ID untuk idempoten dan kembalikan 2xx cepat setelah persist.',
    ],

    'example_payload' => [
        'id'   => 'evt_' . now()->timestamp,
        'type' => 'payment_intent.succeeded',
        'data' => [
            'object' => [
                'id'     => 'pi_01H',
                'status' => 'succeeded',
            ],
        ],
        'provider'   => 'stripe',
        'created_at' => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/stripe',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
