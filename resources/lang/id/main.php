<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Site / Brand
    |--------------------------------------------------------------------------
    */
    'site_name' => 'TenRusl',

    /*
    |--------------------------------------------------------------------------
    | Global Navigation
    |--------------------------------------------------------------------------
    */
    'home_title' => 'Beranda',
    'providers_title' => 'Penyedia',
    'features' => 'Fitur',
    'endpoints' => 'Endpoint',
    'signature' => 'Tanda Tangan',
    'tooling' => 'Perkakas',
    'openapi' => 'OpenAPI',
    'github' => 'GitHub',

    /*
    |--------------------------------------------------------------------------
    | Accessibility / ARIA
    |--------------------------------------------------------------------------
    */
    'aria' => [
        'primary_nav' => 'Navigasi utama',
        'utility_nav' => 'Navigasi utilitas',
        'toggle_menu' => 'Alihkan menu navigasi utama',
        'toggle_theme' => 'Alihkan tema terang dan gelap',
        'skip_to_main' => 'Loncat langsung ke konten utama',
        'language' => 'Ubah bahasa antarmuka',
    ],

    /*
    |--------------------------------------------------------------------------
    | Footer / Legal
    |--------------------------------------------------------------------------
    */
    'terms' => 'Ketentuan',
    'privacy' => 'Privasi',
    'cookies' => 'Cookie',
    'footer_demo' => 'Lingkungan demo arsitektur pembayaran untuk belajar, pengujian, dan menjelaskan alur webhook-first modern.',
    'build' => 'Build',

    /*
    |--------------------------------------------------------------------------
    | SEO Defaults (override per page via layout/seo.blade.php)
    |--------------------------------------------------------------------------
    */
    'meta' => [
        'default_title' => 'Simulator Webhook Pembayaran',
        'default_description' => 'Simulasikan alur pembayaran dunia nyata dengan operasi idempoten, verifikasi tanda tangan webhook, deduplikasi event, dan perilaku retry/backoff yang realistis — semuanya tanpa mengekspos atau bergantung pada kredensial gateway pembayaran live.',
        'default_image_alt' => 'Diagram arsitektur Simulator Webhook Pembayaran yang menampilkan penyedia pembayaran, callback webhook, retry, dan pembaruan status.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Home (Landing)
    |--------------------------------------------------------------------------
    */
    'home' => [
        'title' => 'Simulator Webhook Pembayaran',
        'description' => 'Menstabilkan integrasi pembayaran Anda di sandbox yang aman dan mirip produksi. Latih penanganan Idempotency-Key, verifikasi tanda tangan, deduplikasi event, dan retry dengan backoff sebelum Anda mengarahkan request ke gateway pembayaran yang sebenarnya.',
        'hero' => [
            'heading_prefix' => 'PWS',
            'heading_emph' => 'Idempoten',
            'lede' => 'Simulasikan event pembayaran masuk dari banyak penyedia, verifikasi tanda tangan raw body, deduplikasi retry yang berisik, dan amati strategi backoff yang realistis dari ujung ke ujung — tanpa menyentuh kredensial live atau webhook produksi.',
            'cta_docs' => 'Referensi OpenAPI',
            'cta_features' => 'Jelajahi fitur',
            'cta_github' => 'Lihat source di GitHub',
            'stats' => [
                'providers' => ['label' => 'Penyedia yang disimulasikan'],
                'tests' => ['label' => 'Tes otomatis'],
                'openapi' => ['label' => 'Endpoint terdokumentasi'],
            ],
            'chip' => 'POST : /api/webhooks/mock dengan payload JSON yang ditandatangani',
            'simulate' => 'Simulasikan webhook pembayaran nyata dengan idempotensi, pengecekan tanda tangan, dan logika retry — sebelum Anda menyentuh produksi.',
        ],
        'sections' => [
            'features' => [
                'title' => 'Fitur',
                'lede' => 'Perkuat dan stabilkan integrasi pembayaran Anda dengan simulasi realistis dan dapat diulang yang mencerminkan cara gateway modern mengirim, menandatangani, dan me-retry event webhook di produksi.',
                'ship' => 'Kirim alur pembayaran yang lebih aman tanpa menyentuh kredensial gateway nyata.',
                'items' => [
                    [
                        'title' => 'Idempotensi',
                        'desc' => 'Latih penanganan Idempotency-Key yang benar sehingga request duplikat, replay, dan retry dari klien terselesaikan menjadi satu catatan pembayaran yang konsisten alih-alih merusak state.',
                    ],
                    [
                        'title' => 'Verifikasi Tanda Tangan',
                        'desc' => 'Validasi raw body request dan header penyedia menggunakan HMAC, timestamp, dan secret, memberi Anda tempat yang aman untuk menyempurnakan logika verifikasi tanda tangan sebelum go live.',
                    ],
                    [
                        'title' => 'Deduplikasi & Retry',
                        'desc' => 'Simulasikan pengiriman webhook duplikat dan exponential backoff sehingga Anda dapat merancang handler yang idempoten, tangguh, dan aman dieksekusi berkali-kali tanpa efek samping.',
                    ],
                    [
                        'title' => 'OpenAPI',
                        'desc' => 'Jelajahi dokumentasi OpenAPI interaktif yang menjelaskan setiap endpoint pembayaran dan webhook, lengkap dengan skema, contoh, dan snippet curl siap jalan.',
                    ],
                    [
                        'title' => 'Postman',
                        'desc' => 'Impor koleksi Postman yang dikurasi untuk memukul endpoint, mengubah payload, dan melatih skenario error dari klien API favorit Anda hanya dalam beberapa klik.',
                    ],
                    [
                        'title' => 'Integrasi CI',
                        'desc' => 'Hubungkan simulator ke pipeline CI Anda sehingga tes, linter, dan contract check berjalan di setiap push, menangkap regresi integrasi jauh sebelum mencapai produksi.',
                    ],
                ],
            ],
            'endpoints' => [
                'title' => 'Endpoint',
                'lede' => 'Definisikan permukaan kecil yang realistis: buat pembayaran, polling status, dan terima webhook ala penyedia dengan semantik idempoten.',
                'cards' => [
                    [
                        'title' => 'POST /api/payments',
                        'desc' => 'Buat catatan pembayaran tersimulasikan yang baru. Endpoint ini memerlukan header Idempotency-Key sehingga Anda dapat memverifikasi bagaimana aplikasi Anda mendeduplikasi percobaan pembayaran yang berulang.',
                    ],
                    [
                        'title' => 'GET /api/payments/{id}',
                        'desc' => 'Ambil state terbaru untuk pembayaran tertentu, termasuk transisi status dan event webhook terkait yang telah diproses oleh simulator.',
                    ],
                    [
                        'title' => 'POST /api/webhooks/{provider}',
                        'desc' => 'Terima callback webhook untuk penyedia tertentu (mock, xendit, midtrans, dan lainnya) menggunakan payload, header, dan skema tanda tangan realistis yang disesuaikan dengan tiap integrasi.',
                    ],
                ],
            ],
            'providers' => [
                'title' => 'Penyedia',
                'lede' => 'Coba alur webhook realistis dari banyak gateway pembayaran dalam satu sandbox, tanpa menyentuh kredensial live.',
                'cta_all' => 'Lihat semua penyedia',
                'map' => [
                    'mock' => 'Mock',
                    'xendit' => 'Xendit',
                    'midtrans' => 'Midtrans',
                    'stripe' => 'Stripe',
                    'paypal' => 'PayPal',
                    'paddle' => 'Paddle',
                    'lemonsqueezy' => 'Lemon Squeezy',
                    'airwallex' => 'Airwallex',
                    'tripay' => 'Tripay',
                    'doku' => 'DOKU',
                    'dana' => 'DANA',
                    'oy' => 'OY!',
                    'payoneer' => 'Payoneer',
                    'skrill' => 'Skrill',
                    'amazon_bwp' => 'Amazon BWP',
                ],
            ],
            'signature' => [
                'title' => 'Verifikasi Tanda Tangan',
                'lede' => 'Verifikasi payload webhook mentah dengan tanda tangan HMAC, timestamp, dan pengecekan header yang ketat.',
                'compare' => 'Bandingkan tanda tangan yang dikirim penyedia dengan yang Anda hitung dari raw body request, shared secret, dan timestamp — dalam waktu konstan.',
                'reject' => 'Tolak secara otomatis tanda tangan yang tidak cocok dan timestamp yang sudah kedaluwarsa.',
                'cards' => [
                    [
                        'title' => 'HMAC / Timestamp',
                        'desc' => 'Bereksperimen dengan tanda tangan HMAC bertimestamp yang melindungi dari serangan replay. Lihat bagaimana hash raw body, shared secret, dan header yang ditandatangani berpadu menjadi jejak audit yang dapat diverifikasi untuk setiap event webhook.',
                    ],
                    [
                        'title' => 'Berbasis Header',
                        'desc' => 'Bekerja dengan header dan token spesifik penyedia, dari bearer secret sederhana hingga amplop tanda tangan terstruktur, untuk memastikan aplikasi Anda menolak payload palsu sambil tetap menerima event yang sah.',
                    ],
                ],
            ],
            'tooling' => [
                'title' => 'Perkakas',
                'work' => 'Bekerja dengan stack pengembangan lokal Anda',
                'cards' => [
                    [
                        'title' => 'OpenAPI',
                        'desc' => 'Gunakan penjelajah OpenAPI bawaan untuk memeriksa skema, menghasilkan contoh request, dan mencoba endpoint di browser, sehingga mudah berbagi dan mendokumentasikan alur pembayaran dengan tim.',
                    ],
                    [
                        'title' => 'Postman',
                        'desc' => 'Klon koleksi Postman untuk menjalankan alur terskrip, memparametrisasi environment, dan dengan cepat membandingkan bagaimana berbagai penyedia berperilaku di bawah urutan request yang sama.',
                    ],
                    [
                        'title' => 'CI',
                        'desc' => 'Integrasikan simulator ke job continuous integration Anda sehingga setiap branch dan pull request divalidasi terhadap skenario pembayaran dan webhook yang sama seperti yang Anda harapkan di produksi.',
                    ],
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Providers (Catalog + Search)
    |--------------------------------------------------------------------------
    */
    'providers' => [
        'title' => 'Penyedia',
        'description' => 'Jelajahi penyedia pembayaran yang didukung, periksa format payload webhook mereka, dan bandingkan skema tanda tangan berdampingan sehingga Anda bisa merancang lapisan integrasi yang konsisten dan agnostik terhadap penyedia.',
        'map' => [
            'mock' => 'Mock',
            'xendit' => 'Xendit',
            'midtrans' => 'Midtrans',
            'stripe' => 'Stripe',
            'paypal' => 'PayPal',
            'paddle' => 'Paddle',
            'lemonsqueezy' => 'Lemon Squeezy',
            'airwallex' => 'Airwallex',
            'tripay' => 'Tripay',
            'doku' => 'DOKU',
            'dana' => 'DANA',
            'oy' => 'OY!',
            'payoneer' => 'Payoneer',
            'skrill' => 'Skrill',
            'amazon_bwp' => 'Amazon BWP',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pluralization / Examples
    |--------------------------------------------------------------------------
    */
    'plurals' => [
        'tests' => '{0} Belum ada tes yang didefinisikan|{1} :count tes otomatis|[2,*] :count Tes',
        'items' => '{0} Tidak ada item tersedia|{1} :count item|[2,*] :count item',
    ],
];
