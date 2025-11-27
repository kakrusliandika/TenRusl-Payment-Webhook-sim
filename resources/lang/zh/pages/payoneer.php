<?php

return [

    'hint' => '产品特定通知。',

    'summary' => <<<'TEXT'
Payoneer Checkout 会把异步通知（webhook）发送到你控制的端点，让系统可以在用户浏览器之外安全地对账/同步支付状态。平台允许你定义专用的通知 URL，并选择最适合你技术栈的投递方式——POST（推荐）或 GET，支持 JSON 或表单编码参数。由于具体参数集合以及签名/鉴权模式与产品相关，请把 Payoneer 通知当作一项集成面：记录用于识别事件的请求头/字段，在可用时加入防重放元数据，并在修改状态前先验证真实性。

在运维上，建议先拆分出一个窄而幂等的处理器：持久化不可变（immutable）的事件记录，并尽快返回 2xx。将耗时的业务逻辑放入后台 worker，避免重试风暴。使用去重键（dedup key），并对可用的 timestamp/nonce 强制设置 freshness 窗口，以抵御重放或乱序投递。如果需要更强保证，可在通知 URL 中附加提供方发放的 token（或你自己的随机 secret），并在服务端校验。最后，为团队发布 runbook，说明端点、格式、失败码，以及你针对所用 Payoneer 产品变体实现的具体验真步骤——并与代码一起版本化。
TEXT
    ,

    // Dari view Payoneer
    'docs' => 'https://checkoutdocs.payoneer.com/docs/create-notification-endpoints',

    'signature_notes' => [
        '提供专用的通知端点（推荐 POST）；接收 JSON 或表单数据。',
        '按你的产品变体文档验证真实性（token 或签名字段）；不匹配则拒绝。',
        '如可用，对 timestamp/nonce 强制 freshness，并让处理保持幂等（保存 dedup key）。',
        '快速 ACK（2xx）并把重活交给后台任务；保留审计记录但不要记录 secrets。',
    ],

    'example_payload' => [
        'event'     => 'checkout.transaction.completed',
        'provider'  => 'payoneer',
        'data'      => [
            'orderId'  => 'PO-001',
            'amount'   => 25000,
            'currency' => 'IDR',
            'status'   => 'COMPLETED',
        ],
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
            'path'   => '/api/webhooks/payoneer',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
