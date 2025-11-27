<?php

return [

    'hint' => 'Verify Webhook Signature API。',

    'summary' => <<<'TEXT'
PayPal 要求对每个 webhook 进行服务端验证，使用官方的 Verify Webhook Signature API。你的监听器必须提取通知中携带的请求头—`PAYPAL-TRANSMISSION-ID`、`PAYPAL-TRANSMISSION-TIME`、`PAYPAL-CERT-URL`、`PAYPAL-TRANSMISSION-SIG`—以及你的 `webhook_id` 和 **原始**请求体（`webhook_event`）。将这些内容 POST 到验证接口，并且仅当 PayPal 返回成功结果时才接受该事件。这取代了较旧的验证机制，并提升各 REST 产品之间的一致性。

将消费者设计为快速且幂等的“门禁”：先验签，再持久化事件记录，快速返回 2xx，然后把重活投递到队列。对任何本地检查使用 constant-time 比较，并在转发给 PayPal 时保持 raw bytes 不变，避免因重新序列化带来的细微错误。围绕 `PAYPAL-TRANSMISSION-TIME` 设置严格的时间容忍窗口以缩小重放窗口，并仅记录最少的审计信息（request ID、验证结果、body 哈希—不记录 secrets）。采用该模式，重复投递与部分故障也不会导致重复处理，且在故障处置期间审计轨迹仍然可信。
TEXT
    ,

    // Dari view PayPal
    'docs' => 'https://developer.paypal.com/docs/api/webhooks/v1/',

    'signature_notes' => [
        '收集请求头：PAYPAL-TRANSMISSION-ID、PAYPAL-TRANSMISSION-TIME、PAYPAL-CERT-URL、PAYPAL-TRANSMISSION-SIG；保留 raw body。',
        '携带上述值以及 webhook_id、webhook_event 调用 Verify Webhook Signature API；仅在成功时接受。',
        '将验证作为门禁；设置短时间容忍以缓解重放，并让消费者保持幂等。',
        '尽快返回 2xx，将重活入队；只记录最少诊断信息（不含 secrets）。',
    ],

    'example_payload' => [
        'id'          => 'WH-' . now()->timestamp,
        'event_type'  => 'PAYMENT.CAPTURE.COMPLETED',
        'resource'    => [
            'id'     => 'CAP-001',
            'status' => 'COMPLETED',
        ],
        'provider'    => 'paypal',
        'create_time' => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/paypal',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
