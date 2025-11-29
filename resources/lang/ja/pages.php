<?php

return [

    'back_to_providers' => 'プロバイダ一覧に戻る',
    'view_details' => '詳細を表示',
    'breadcrumb' => 'パンくずリスト',

    /*
    |--------------------------------------------------------------------------
    | Providers Page / Search
    |--------------------------------------------------------------------------
    */
    'search_providers' => 'プロバイダを検索...',
    'no_results' => '検索条件に一致するプロバイダはありません。',

    /*
    |--------------------------------------------------------------------------
    | Payments (cards & partials)
    |--------------------------------------------------------------------------
    */
    'provider_default_name' => 'プロバイダ',
    'provider_label' => 'プロバイダ',
    'provider_endpoints' => 'エンドポイント',
    'signature_notes_title' => '署名メモ',
    'example_payload_title' => 'サンプルペイロード',
    'view_docs' => 'ドキュメントを表示',

    'create_payment' => '支払いを作成（idempotent）',
    'get_payment' => '支払いステータスを取得',
    'receive_webhook' => 'Webhook を受信',

    'endpoint_list' => [
        'create_payment' => [
            'method' => 'POST',
            'path' => '/api/payments',
            'note' => 'Idempotency-Key が必須',
        ],
        'get_payment' => [
            'method' => 'GET',
            'path' => '/api/payments/{id}',
            'note' => '支払いステータスを取得',
        ],
        'receive_webhook' => [
            'method' => 'POST',
            'path' => '/api/webhooks/{provider}',
            'note' => 'mock/xendit/midtrans/…',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Errors / Empty states
    |--------------------------------------------------------------------------
    */
    'not_found' => 'お探しのページは見つかりませんでした。',
    'server_error' => '予期しないサーバーエラーが発生しました。もう一度お試しください。',

    /*
    |--------------------------------------------------------------------------
    | Pluralization examples used on pages
    |--------------------------------------------------------------------------
    | Use via: trans_choice('pages.counts.results', $count, ['count' => $count])
    */
    'counts' => [
        'results' => '{0} 結果はありません|{1} :count 件の結果|[2,*] :count 件の結果',
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback summary untuk provider yang belum punya file sendiri
    |--------------------------------------------------------------------------
    */
    'default_summary' => '署名付き Webhook、idempotent なフロー、安全なペイロードを備えたシミュレートされた決済プロバイダで、ローカルおよび CI テストに利用できます。',
];
