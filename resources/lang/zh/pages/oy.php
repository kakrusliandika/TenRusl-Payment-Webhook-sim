<?php

return [

    'hint' => '特定提供方回调签名。',

    'summary' => <<<'TEXT'
OY! 回调是更广泛安全体系的一部分，围绕已注册的 API Key 以及合作方请求的源 IP allowlisting 来构建。OY! 还提供 Authorization Callback 功能，让你在回调到达系统之前先进行控制与审批，增加一道明确的闸门以防止非预期的状态变更。即便如此，你仍应在验证通过前将所有入站回调视为不可信，强制 freshness（timestamp/nonce 窗口），并将消费者实现为幂等，以确保重试与乱序投递仍然安全。

由于不同公共提供方对回调的签名方式各不相同，我们的模拟器展示了一个加固的基线：使用 HMAC 请求头（例如 `X-Callback-Signature`），对“原样的 raw request body”结合共享密钥进行计算。这体现了你在生产会用到的同一套原则：raw 字节哈希（不做重序列化）、constant-time 比较、短的 replay 窗口。配合一个小型去重存储和快速的 2xx 确认，可以在保持提供方重试逻辑健康的同时避免重复副作用。

在运维层面，建议维护审计轨迹（接收时间、验签结果、body 哈希——不记录密钥），安全轮换密钥，并监控验签失败率。如果依赖 allowlist，请记住它可能变化；密码学校验（或 OY 的显式授权闸门）应始终作为主要信任锚点。保持端点范围小、行为可预测且文档完善，便于其他服务与团队成员可靠复用。
TEXT
    ,

    // dari view OY
    'docs' => 'https://docs.oyindonesia.com/',

    'signature_notes' => [
        '采用 OY! 的安全策略：已注册的 API key + 合作方请求的源 IP allowlisting。',
        '利用 Authorization Callback（控制台/仪表盘）在回调进入系统前进行审批。',
        '在本模拟器中，以最佳实践模型验证 `X-Callback-Signature = HMAC-SHA256(raw_body, secret)`；并应用 constant-time 比较与 freshness 校验。',
        '处理需保持幂等，并及时返回 2xx，以保持提供方重试机制健康。',
    ],

    'example_payload' => [
        'id' => 'evt_'.now()->timestamp,
        'type' => 'payment.completed',
        'provider' => 'oy',
        'data' => [
            'partner_trx_id' => 'PRT-001',
            'amount' => 25000,
            'currency' => 'IDR',
            'status' => 'COMPLETED',
        ],
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
            'path' => '/api/webhooks/oy',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
