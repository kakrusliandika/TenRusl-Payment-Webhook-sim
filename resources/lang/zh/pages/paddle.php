<?php

return [

    'hint' => '公钥签名（Classic）/ 密钥（Billing）。',

    'summary' => <<<'TEXT'
Paddle Billing 会为每个 webhook 添加 `Paddle-Signature` 请求头，其中包含 Unix 时间戳（`ts`）与签名（`h1`）。手动验签时，将 `ts`、冒号以及“原样的 raw request body”（按收到的字节完全一致）拼接成 signed payload；然后用你的 notification destination 对应的密钥计算 HMAC-SHA256，并用 constant-time（timing-safe）方式与 `h1` 进行比较。Paddle 会为每个 notification destination 生成独立密钥——把它当作密码对待，切勿提交到代码仓库。

建议使用官方 SDK 或自定义验签中间件，在任何解析之前先完成校验。由于时间与 body 变换是常见坑点，请确保框架能够提供 raw bytes（例如 Express 的 `express.raw({ type: 'application/json' })`），并对 `ts` 设置较短的容忍窗口以防重放（replay）。验签通过后，应快速 ACK（2xx），保存 event ID 以保证幂等，并将耗时工作转交后台任务。这样能提升投递可靠性，并在重试时避免重复副作用。

从 Paddle Classic 迁移时请注意：验签已从公钥签名转为 Billing 的基于密钥的 HMAC。相应更新 runbook 与密钥管理，并在上线变更时监控验签指标。清晰的日志（不包含密钥）以及确定性的错误响应，将显著简化故障处置与合作方支持。
TEXT
    ,

    // Dari view Paddle
    'docs' => 'https://developer.paddle.com/webhooks/signature-verification',

    'signature_notes' => [
        '读取 `Paddle-Signature` 请求头；解析出 `ts` 与 `h1`。',
        '构造 signed payload = `ts + ":" + <raw request body>`；使用 endpoint 密钥计算 HMAC。',
        '用 timing-safe 函数将你的哈希与 `h1` 对比；对 `ts` 施加短容忍窗口以防重放。',
        '优先使用官方 SDK 或验签 middleware；仅在验签通过后才解析 JSON。',
    ],

    'example_payload' => [
        'event_id' => 'evt_'.now()->timestamp,
        'event_type' => 'transaction.completed',
        'provider' => 'paddle',
        'data' => [
            'transaction_id' => 'txn_001',
            'amount' => 25000,
            'currency_code' => 'IDR',
            'status' => 'completed',
        ],
        'occurred_at' => now()->toIso8601String(),
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
            'path' => '/api/webhooks/paddle',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
