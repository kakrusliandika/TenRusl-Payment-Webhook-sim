<?php

return [

    'hint' => '基于 x-timestamp + body 计算 HMAC-SHA256。',

    'summary' => <<<'TEXT'
Airwallex 的 Webhook 会带有签名，这样你就可以在访问数据库之前验证请求的真实性（authenticity）和完整性（integrity）。每个请求都包含两个关键头：`x-timestamp` 和 `x-signature`。要验证一条消息，先按原样读取收到的 HTTP 原始请求体（raw body），将 `x-timestamp` 的值（字符串形式）与该原始请求体拼接，作为摘要输入，然后使用 SHA-256 计算 HMAC，密钥为通知 URL 的共享密钥（shared secret）。Airwallex 期望结果是一个 **十六进制摘要（hex digest）**；需要将该值与 `x-signature` 头进行比较，并使用恒定时间比较（constant-time comparison）以避免时间侧信道泄露。如果签名不匹配，或者时间戳缺失/无效，就要安全失败（fail closed），返回非 2xx 响应。

由于重放攻击（replay）在任何 Webhook 系统中都是真实存在的风险，因此应当对 `x-timestamp` 应用一个“新鲜度窗口”（freshness window）。拒绝过旧或过于超前的时间戳，并存储已处理的事件 ID，以在下游去重相关副作用（即在应用层实现幂等性）。在验证通过之前，一定要把 payload 当作不可信数据；不要在哈希前重新对 JSON 进行序列化，而是要使用到达时的原始字节，这样可以避免因为空白字符或字段顺序的细微差异导致的签名不一致。当验证成功时，应尽快返回 `2xx` 响应；将耗时的工作放到异步任务中处理，以保持重试逻辑的良好行为并减少意外重复。

在本地环境和 CI 流程中，Airwallex 提供了一流的工具：你可以在控制台中配置通知 URL，预览示例 payload，并向你的 endpoint **发送测试事件**。调试时建议记录收到的 `x-timestamp`、你计算出的签名预览（切记不要记录密钥）以及任何存在的事件 ID。如果你轮换 secret key，应当安全地分步上线，并监控签名错误率。最后，建议把完整链路——验证、去重、重试以及错误响应——记录在文档中，这样团队成员就可以在相同的原始请求体哈希规则和时间窗口下复现同样的结果。
TEXT
    ,

    'docs' => 'https://www.airwallex.com/docs/developer-tools/webhooks/listen-for-webhook-events',

    'signature_notes' => [
        '从请求头中提取 `x-timestamp` 和 `x-signature`。',
        '构造 value_to_digest = <x-timestamp> + <HTTP 原始请求体>（字节完全一致）。',
        '计算 expected = HMAC-SHA256(value_to_digest, <webhook secret>)，以 HEX 字符串表示；使用恒定时间比较与 `x-signature` 对比。',
        '如果签名不匹配或时间戳已过期则拒绝请求；同时对已处理的事件 ID 做去重以确保幂等性。',
    ],

    'example_payload' => [
        'id'       => 'evt_' . now()->timestamp,
        'type'     => 'payment_intent.succeeded',
        'data'     => [
            'payment_intent_id' => 'pi_awx_001',
            'amount'            => 25000,
            'currency'          => 'IDR',
            'status'            => 'succeeded',
        ],
        'provider'   => 'airwallex',
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
            'path'   => '/api/webhooks/airwallex',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
