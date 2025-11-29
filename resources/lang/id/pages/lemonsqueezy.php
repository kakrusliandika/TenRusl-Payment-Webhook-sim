<?php

return [

    'hint' => 'Header signature HMAC.',

    'summary' => <<<'TEXT'
Lemon Squeezy menandatangani setiap webhook dengan HMAC sederhana atas **raw request body**. Pengirim memakai “signing secret” webhook-mu untuk menghasilkan HMAC SHA-256 berbentuk **hex digest**; digest itu dikirim lewat header `X-Signature`. Tugasmu adalah membaca bytes body persis seperti diterima (tanpa re-stringify, tanpa perubahan whitespace), menghitung HMAC yang sama dengan secret-mu, mengeluarkannya sebagai string **hex**, lalu membandingkannya dengan `X-Signature` menggunakan fungsi constant-time. Jika nilainya berbeda — atau header hilang — tolak request sebelum menyentuh logika bisnis apa pun.

Karena default framework sering mem-parse body sebelum kamu sempat meng-hash-nya, pastikan route kamu memberi akses ke raw bytes (misalnya konfigurasi “raw body” di Node/Express). Anggap verifikasi sebagai gerbang: hanya setelah lolos, baru parse JSON dan update state. Buat handler idempotent agar retry/duplikat tidak menerapkan side effect dua kali, dan simpan diagnostik minimal (panjang header yang diterima, hasil verifikasi, event id) alih-alih secret. Untuk testing lokal, gunakan test events dari Lemon Squeezy dan simulasikan kegagalan untuk memastikan perilaku retry/backoff. Dokumentasikan alur end-to-end — verifikasi, deduplikasi, dan pemrosesan asinkron — agar tim bisa mereproduksi hasil yang konsisten lintas environment.
TEXT
    ,

    // dari view Lemon Squeezy
    'docs' => 'https://docs.lemonsqueezy.com/help/webhooks/signing-requests',

    'signature_notes' => [
        'Baca `X-Signature` (HMAC-SHA256 **hex** dari raw body) dan dapatkan raw request bytes.',
        'Hitung hex HMAC memakai signing secret dan bandingkan dengan fungsi timing-safe.',
        'Tolak jika mismatch/header hilang; parse JSON hanya setelah verifikasi sukses.',
        'Pastikan framework menyediakan raw body (tanpa re-serialisasi); buat handler idempotent dan log diagnostik minimal.',
    ],

    'example_payload' => [
        'meta' => ['event_name' => 'order_created'],
        'data' => ['id' => 'ord_001', 'status' => 'paid'],
        'provider' => 'lemonsqueezy',
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
            'path' => '/api/webhooks/lemonsqueezy',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
