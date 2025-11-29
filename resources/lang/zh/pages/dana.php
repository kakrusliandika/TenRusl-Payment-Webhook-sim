<?php

return [

    'hint' => 'RSA（使用 DANA 公钥验证）。',

    'summary' => <<<'TEXT'
DANA 采用**非对称**签名方案：请求由私钥签名，集成方使用官方 **DANA 公钥**进行验证。实际流程通常是：从 webhook 头中取出签名（例如 `X-SIGNATURE`），对其进行 base64 解码，然后使用 RSA-2048 + SHA-256，将接收到的原始 HTTP 请求体（raw body）与该签名进行校验。只有当校验结果为真时，才应认为 payload 是可信/真实的。如果校验失败——或签名/头缺失——请返回非 2xx 状态码并停止处理。

由于 webhook 可能会重试投递或乱序到达，你的处理器应设计为幂等：持久化唯一事件标识并对重复事件直接短路；校验 timestamp/nonce 的新鲜度以降低重放（replay）风险；并在签名验证成功之前，把所有字段都视为不可信。避免在验证前重新序列化 JSON；务必对“线上传来的原始字节”进行哈希/验证。不要在日志中记录 secret 与私钥；如果必须记录，只保留高层诊断信息（验证结果、body 的哈希、事件 ID），并确保日志落盘时被安全保护。

面向团队，建议发布一份简短 runbook，涵盖：如何加载或轮换 DANA 公钥、在各语言/运行时中如何做验证、你们集成所遵循的精确 string-to-sign 规则，以及哪些属于永久失败、哪些属于瞬时失败。再配合健壮的 retry/backoff 策略、有界工作队列、健康检查，以及对验证失败的告警。最终得到的 webhook consumer 将能在高负载下保持安全、对重试更具韧性，并符合 DANA 设计要求的加密验证流程。
TEXT
    ,

    // dari view DANA
    'docs' => 'https://dashboard.dana.id/api-docs-v2/guide/authentication',

    'signature_notes' => [
        '对 `X-SIGNATURE` 头的值进行 base64 解码。',
        '使用官方 DANA 公钥，对“完全一致的 raw HTTP body”执行 RSA-2048 + SHA-256 验签；仅在验证为真时接受。',
        '拒绝任何缺失/无效签名或 payload 不合法的 webhook；在验证成功前绝不信任数据。',
    ],

    'example_payload' => [
        'id' => 'evt_'.now()->timestamp,
        'type' => 'payment.paid',
        'provider' => 'dana',
        'data' => [
            'transaction_id' => 'DANA-001',
            'amount' => 25000,
            'currency' => 'IDR',
            'status' => 'SUCCESS',
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
            'path' => '/api/webhooks/dana',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
