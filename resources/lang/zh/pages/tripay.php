<?php

return [

    'hint' => 'X-Callback-Signature.',

    'summary' => <<<'TEXT'
TriPay 会向你配置的 URL 投递回调（callback），并附带用于标识事件与验证发送方的请求头。尤其是，回调会携带 `X-Callback-Event`（例如 `payment_status`），以及用于签名校验的 `X-Callback-Signature`（具体校验方式以 TriPay 文档为准）。你的消费者（consumer）应读取这些请求头，完成真实性验证后，才更新内部状态。

端点设计应快速且具备幂等性。若包含时间戳/nonce，请设置较短的有效窗口，并维护一个轻量的去重存储（以 reference 或事件标识为键）。在记录事件后尽快返回 2xx，然后将副作用处理放到异步任务中。为便于透明化与事故处理，保留审计记录：接收时间、事件元数据与验签结果，但不要在日志中记录任何密钥/秘密信息。
TEXT
    ,

    // dari view Tripay
    'docs' => 'https://tripay.co.id/developer',

    'signature_notes' => [
        '检查 `X-Callback-Event`（例如 `payment_status`）与 `X-Callback-Signature`.',
        '按 TriPay 文档验证签名；若不匹配或缺少请求头则拒绝处理.',
        '保持幂等处理（按 reference/event ID 去重）并快速确认（2xx）.',
    ],

    'example_payload' => [
        'reference' => 'TRX-001',
        'status'    => 'PAID',
        'amount'    => 125000,
        'currency'  => 'IDR',
        'provider'  => 'tripay',
        'sent_at'   => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/tripay',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
