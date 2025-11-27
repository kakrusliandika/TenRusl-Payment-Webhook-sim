<?php

return [

    'back_to_providers' => 'Kembali ke penyedia',
    'view_details'      => 'Lihat detail',
    'breadcrumb'        => 'Breadcrumb',

    /*
    |--------------------------------------------------------------------------
    | Providers Page / Search
    |--------------------------------------------------------------------------
    */
    'search_providers' => 'Cari penyedia...',
    'no_results'       => 'Tidak ada penyedia yang cocok dengan pencarian Anda.',

    /*
    |--------------------------------------------------------------------------
    | Payments (cards & partials)
    |--------------------------------------------------------------------------
    */
    'provider_default_name' => 'Penyedia',
    'provider_label'        => 'Penyedia',
    'provider_endpoints'    => 'Endpoint',
    'signature_notes_title' => 'Catatan tanda tangan',
    'example_payload_title' => 'Contoh payload',
    'view_docs'             => 'Lihat dokumentasi',

    'create_payment'  => 'Buat pembayaran (idempotent)',
    'get_payment'     => 'Ambil status pembayaran',
    'receive_webhook' => 'Terima webhook',

    'endpoint_list' => [
        'create_payment' => [
            'method' => 'POST',
            'path'   => '/api/payments',
            'note'   => 'Wajib Idempotency-Key',
        ],
        'get_payment' => [
            'method' => 'GET',
            'path'   => '/api/payments/{id}',
            'note'   => 'Ambil status pembayaran',
        ],
        'receive_webhook' => [
            'method' => 'POST',
            'path'   => '/api/webhooks/{provider}',
            'note'   => 'mock/xendit/midtrans/…',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Errors / Empty states
    |--------------------------------------------------------------------------
    */
    'not_found'    => 'Halaman yang Anda cari tidak ditemukan.',
    'server_error' => 'Terjadi kesalahan server yang tidak terduga. Silakan coba lagi.',

    /*
    |--------------------------------------------------------------------------
    | Pluralization examples used on pages
    |--------------------------------------------------------------------------
    | Use via: trans_choice('pages.counts.results', $count, ['count' => $count])
    */
    'counts' => [
        'results' => '{0} Tidak ada hasil|{1} :count hasil|[2,*] :count hasil',
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback summary untuk provider yang belum punya file sendiri
    |--------------------------------------------------------------------------
    */
    'default_summary' => 'Penyedia pembayaran ter-simulasi dengan webhook bertanda tangan, alur idempoten, dan payload aman untuk pengujian lokal maupun di CI.',
];
