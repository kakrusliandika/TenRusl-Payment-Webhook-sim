<?php

return [

    'hint' => '带时间戳的签名请求头。',

    'summary' => <<<'TEXT'
Stripe 会对每个 webhook 请求进行签名，并将计算得到的签名放在 `Stripe-Signature` 请求头中。你的端点必须在执行任何业务逻辑之前先验证该请求。使用 Stripe 官方库时，向验证例程传入 3 个输入：原始 raw request body、`Stripe-Signature` 请求头值，以及你的 endpoint secret。只有验证成功时才继续处理；否则返回 non-2xx 并停止处理。当无法使用官方库时，需要按文档实现手动验签，并包含时间戳容忍度（timestamp tolerance）检查，以降低重放（replay）风险。

将签名验证视为严格的门禁。处理器应保持幂等（保存 event ID），在持久化后尽快返回 2xx，并把重活交给后台任务。确保框架可以拿到 **raw bytes**——不要在哈希前重新序列化 JSON，因为空白或字段顺序的任何变化都会导致验签失败。最后只记录最小化诊断信息（验签结果、事件类型、body 哈希——不包含密钥），并在密钥轮换或端点变更期间监控失败率。
TEXT
    ,

    // dari view Stripe
    'docs' => 'https://docs.stripe.com/webhooks',

    'signature_notes' => [
        '读取 `Stripe-Signature` 请求头；从 Stripe 控制台获取 endpoint secret。',
        '使用官方库校验：传入 raw request body、`Stripe-Signature` 和 endpoint secret。',
        '手动校验时，设置 timestamp tolerance 以降低重放风险，并用 timing-safe 方法比较签名。',
        '仅在成功时接受；保存 event ID 保证幂等，并在持久化后尽快返回 2xx。',
    ],

    'example_payload' => [
        'id'   => 'evt_' . now()->timestamp,
        'type' => 'payment_intent.succeeded',
        'data' => [
            'object' => [
                'id'     => 'pi_01H',
                'status' => 'succeeded',
            ],
        ],
        'provider'   => 'stripe',
        'created_at' => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/stripe',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
