<?php

return [

    'back_to_providers' => '返回服务商',
    'view_details'      => '查看详情',
    'breadcrumb'        => '面包屑导航',

    /*
    |--------------------------------------------------------------------------
    | Providers Page / Search
    |--------------------------------------------------------------------------
    */
    'search_providers' => '搜索服务商...',
    'no_results'       => '没有与您的搜索匹配的服务商。',

    /*
    |--------------------------------------------------------------------------
    | Payments (cards & partials)
    |--------------------------------------------------------------------------
    */
    'provider_default_name' => '服务商',
    'provider_label'        => '服务商',
    'provider_endpoints'    => '端点',
    'signature_notes_title' => '签名说明',
    'example_payload_title' => '示例负载',
    'view_docs'             => '查看文档',

    'create_payment'  => '创建支付（幂等）',
    'get_payment'     => '获取支付状态',
    'receive_webhook' => '接收 Webhook',

    'endpoint_list' => [
        'create_payment' => [
            'method' => 'POST',
            'path'   => '/api/payments',
            'note'   => '需要 Idempotency-Key',
        ],
        'get_payment' => [
            'method' => 'GET',
            'path'   => '/api/payments/{id}',
            'note'   => '获取支付状态',
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
    'not_found'    => '无法找到您要访问的页面。',
    'server_error' => '发生意外的服务器错误，请重试。',

    /*
    |--------------------------------------------------------------------------
    | Pluralization examples used on pages
    |--------------------------------------------------------------------------
    | Use via: trans_choice('pages.counts.results', $count, ['count' => $count])
    */
    'counts' => [
        'results' => '{0} 无结果|{1} :count 个结果|[2,*] :count 个结果',
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback summary untuk provider yang belum punya file sendiri
    |--------------------------------------------------------------------------
    */
    'default_summary' => '用于本地和 CI 测试的模拟支付服务商，具有已签名的 Webhook、幂等流程以及安全的负载。',
];
