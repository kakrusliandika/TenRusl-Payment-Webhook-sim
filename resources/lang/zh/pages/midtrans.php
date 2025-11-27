<?php

return [

    'hint' => 'signature_key 校验。',

    'summary' => <<<'TEXT'
Midtrans 会在每个 HTTP(S) 通知中包含计算得到的 `signature_key`，方便你在处理前验证来源。其公式明确且稳定：
    SHA512(order_id + status_code + gross_amount + ServerKey)
请使用通知 body 中的原始字段值（按字符串使用）与你的私有 `ServerKey` 拼接生成输入字符串，然后计算 SHA-512 的 hex digest，并用 constant-time（定时安全）方式与 `signature_key` 比较。若校验失败，应直接丢弃通知。对于真实消息，请使用文档字段（例如 `transaction_status`）驱动你的状态机——快速 ACK（2xx）、将重任务入队，并在重试或乱序投递情况下保持更新的幂等性。

常见陷阱有两类：格式与类型转换。拼接字符串时，`gross_amount` 必须完全保持收到的原样格式（不要本地化、不要改变小数位），并避免 trim 或改动空白/换行。建议按事件或按订单保存去重键以避免竞态；记录验签结果和 body 哈希用于审计，但不要泄露任何 secret。配合端点 rate limiting 与清晰的失败码，便于监控区分临时故障（可重试）与永久拒绝（签名无效）。
TEXT
    ,

    // dari view midtrans
    'docs' => 'https://docs.midtrans.com/docs/https-notification-webhooks',

    'signature_notes' => [
        '从 body 取 `order_id`、`status_code`、`gross_amount`（按字符串）并拼接你的 `ServerKey`。',
        '计算 `SHA512(order_id + status_code + gross_amount + ServerKey)` 并与 `signature_key` 做 constant-time 比较。',
        '不匹配则拒绝；匹配则依据 `transaction_status` 更新状态。保持幂等处理并尽快返回 2xx。',
        '拼接时注意不要改变 `gross_amount` 的格式，避免混入多余空白字符。',
    ],

    'example_payload' => [
        'order_id'           => 'ORDER-001',
        'status_code'        => '200',
        'gross_amount'       => '25000.00',
        'transaction_status' => 'settlement',
        'signature_key'      => '<sha512>',
        'provider'           => 'midtrans',
        'sent_at'            => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/midtrans',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
