<?php

return [

    'hint' => 'HMAC 签名请求头.',

    'summary' => <<<'TEXT'
Lemon Squeezy 会对每个 webhook 进行签名：对**原始请求体（raw request body）**计算一个简单的 HMAC。发送方使用你的 webhook “signing secret” 生成 SHA-256 HMAC 的 **hex digest**，并将该 digest 放在 `X-Signature` 请求头中。你的任务是按原样读取收到的 body 字节（不要重新 stringify，不要改变空白字符），用相同的 secret 计算同样的 HMAC，以 **hex** 字符串输出，并使用 constant-time（定时安全）方式与 `X-Signature` 比较。如果值不一致——或请求头缺失——应在进入任何业务逻辑之前直接拒绝请求。

由于不少框架默认会在你验签前解析 body，请确保你的路由能拿到 raw bytes（例如在 Node/Express 配置 “raw body” 处理）。把验签当作一道闸门：只有通过后才解析 JSON 并更新状态。让处理器具备幂等性，避免重试/重复投递导致副作用被重复执行；记录最少的诊断信息（收到的请求头长度、验签结果、事件 ID），而不是任何 secret。本地测试可使用 Lemon Squeezy 的测试事件，并模拟失败以确认 retry/backoff 行为。把端到端流程（验签、去重、异步处理）文档化，方便团队在不同环境中复现一致结果。
TEXT
    ,

    // dari view Lemon Squeezy
    'docs' => 'https://docs.lemonsqueezy.com/help/webhooks/signing-requests',

    'signature_notes' => [
        '读取 `X-Signature`（raw body 的 HMAC-SHA256 **hex**）并获取原始请求字节。',
        '用 signing secret 计算 hex HMAC，并用 timing-safe 函数进行比较。',
        '不匹配/缺少请求头则拒绝；仅在验签成功后解析 JSON。',
        '确保框架提供 raw body（不要重新序列化）；处理器要幂等并仅记录最少诊断信息。',
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
