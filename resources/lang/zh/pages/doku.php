<?php

return [

    'hint' => '使用 Client-Id/Request-* 头的签名。',

    'summary' => <<<'TEXT'
DOKU 通过一种基于请求头的规范化（canonical）签名来保护 HTTP 通知，你必须在处理任何 payload 之前先完成验签。每次回调都会带有 `Signature` 头，其值形如 `HMACSHA256=<base64>`. 要重建期望值，先计算请求体的 `Digest`：对原始 JSON 字节（raw bytes）做 SHA-256，再将结果进行 base64 编码。接着，按**完全一致**的顺序与拼写，构造一个以换行分隔的字符串，包含以下 5 个组件：
  • `Client-Id:<value>`
  • `Request-Id:<value>`
  • `Request-Timestamp:<value>`
  • `Request-Target:<path-of-your-notification-url>`（例如 `/payments/notifications`）
  • `Digest:<base64-of-SHA256(body)>`
然后使用你的 DOKU Secret Key 作为密钥，对该 canonical 字符串计算 HMAC-SHA256，将结果 base64 编码并加上前缀 `HMACSHA256=`。最后用 constant-time 的方式与 `Signature` 头进行比较。任何不匹配、组件缺失或格式错误都应视为认证失败，必须立即拒绝该请求。

为了提升可靠性与安全性，对有效通知要快速确认（2xx），并把重任务下放到后台作业，避免触发重试。通过记录已处理的标识符（例如 `Request-Id` 或请求体中的事件 ID）让消费者具备幂等性。校验新鲜度：`Request-Timestamp` 应处于严格的时间窗口内以防重放；同时确保 `Request-Target` 与你的真实路由路径一致，避免规范化问题导致验签错误。解析时遵循 DOKU 的非严格建议：忽略未知字段，优先支持 schema 演进而不是脆弱的解析器。故障排查时，可记录必需请求头是否存在、计算得到的 digest/signature（永不记录 secret）、以及请求体哈希，便于审计而不泄露敏感信息。
TEXT
    ,

    // dari view DOKU
    'docs' => 'https://developers.doku.com/get-started-with-doku-api/signature-component/non-snap/signature-component-from-request-header',

    'signature_notes' => [
        '读取请求头：`Client-Id`, `Request-Id`, `Request-Timestamp`, `Signature`，并推导 `Request-Target`（你的路由 path）。',
        '计算 `Digest = base64( SHA256(raw JSON body) )`。',
        '按行构造 canonical 字符串：Client-Id、Request-Id、Request-Timestamp、Request-Target、Digest（该顺序，每项独占一行，末尾不加额外换行）。',
        '计算 expected = `HMACSHA256=base64( HMAC-SHA256(canonical, SecretKey) )`；用 constant-time 方式与 `Signature` 比较。',
        '强制校验 timestamp 新鲜度；保证幂等处理；快速 ACK（2xx）并把重任务下放到后台。',
    ],

    'example_payload' => [
        'order'       => ['invoice_number' => 'INV-001'],
        'transaction' => ['status' => 'SUCCESS'],
        'provider'    => 'doku',
        'sent_at'     => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/doku',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
