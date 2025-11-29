<?php

return [

    'hint' => '回调令牌签名。',

    'summary' => <<<'TEXT'
Xendit 通过每个账户独有的令牌对 webhook 事件进行“签名”，该令牌会在 `x-callback-token` 请求头中携带。你的集成必须将该请求头与 Xendit 控制台中获取的令牌进行比对，并拒绝任何缺失令牌或令牌不匹配的请求。某些 webhook 产品还会提供 `webhook-id`，你可以将其保存下来，以在重试（retry）场景中防止重复处理。

在运维上，应将校验作为第一步，持久化不可变（immutable）的事件记录，尽快返回 2xx，并将繁重工作交给队列处理。使用 `webhook-id`（或你自己的键）实现幂等，并在提供 timestamp 元数据时设置严格的时间窗口。把完整链路（校验、去重、重试与错误码）文档化，确保团队与各服务在不同环境下都能一致集成。
TEXT
    ,

    // dari view Xendit
    'docs' => 'https://docs.xendit.co/docs/handling-webhooks',

    'signature_notes' => [
        '将 `x-callback-token` 与你在 Xendit 控制台获取的唯一令牌对比；不匹配则拒绝。',
        '如有 `webhook-id` 则用于去重；将校验视为解析 JSON 之前的硬门禁。',
        '尽快返回 2xx 并延后重任务；仅记录最小化诊断信息且不泄露密钥。',
    ],

    'example_payload' => [
        'id' => 'evt_xnd_'.now()->timestamp,
        'event' => 'invoice.paid',
        'data' => [
            'id' => 'inv_001',
            'status' => 'PAID',
        ],
        'provider' => 'xendit',
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
            'path' => '/api/webhooks/xendit',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
