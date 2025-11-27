<?php

return [

    'hint' => '每个请求都带有 x-amzn-signature。',

    'summary' => <<<'TEXT'
Buy with Prime（BWP）会对每个 webhook 进行签名，便于你确认其确实来自 Amazon，且在传输过程中未被篡改。每个请求都会在 `x-amzn-signature` 头中携带数字签名。你的处理器必须针对对应的事件类型与环境，严格按照 BWP 文档所述方式重建期望签名；若不匹配，则应拒绝该调用。将请求中附带的 timestamp/nonce 视为反重放（anti-replay）策略的一部分：强制执行严格的有效时间窗口，并保存已处理的标识符以避免重复。

从运行角度看，应将该 endpoint 设计为快速且确定性（deterministic）：先验证，再在安全记录后用 `2xx` 确认，应把最重的工作放到异步执行。即便你依赖 allowlist，也要记住 IP 与网络可能变化——加密验证才是主要的信任锚点。保留安全的审计轨迹（请求 ID、是否带签名、验证结果、以及请求体的哈希——但不要记录密钥）。本地测试时，可以在环境开关后对验证步骤进行 stub，但必须确保生产路径始终校验签名。在轮换密钥或更新规范化（canonicalization）规则时，应谨慎前滚发布、监控错误率，并精确记录你实现的头部集合以及哈希/规范化方式，确保你的技术栈内其他服务保持一致。

从集成易用性的角度，暴露**清晰的失败原因**（签名无效、时间戳过期、请求格式错误），并返回稳定的错误码，使重试行为可预测。结合应用层幂等与反重放保护，即使在重试、流量突增或部分故障的情况下，也能让下游支付状态流转保持安全。
TEXT
    ,

    // Dari view amazon_bwp.blade.php
    'docs' => 'https://documents.buywithprime.amazon.com/bwp-api/docs/subscribe-to-events',

    'signature_notes' => [
        '从请求头中读取 `x-amzn-signature`。',
        '严格按 Buy with Prime 的定义（官方文档中的算法/规范化）重建期望签名；不匹配则拒绝请求。',
        '如果提供了 timestamp/nonce，则强制执行严格的新鲜度窗口以降低重放风险；保存已处理的 ID 以避免重复。',
    ],

    'example_payload' => [
        'eventType' => 'ORDER_COMPLETED',
        'data'      => [
            'orderId'  => 'BWP-001',
            'status'   => 'COMPLETED',
            'amount'   => 25000,
            'currency' => 'IDR',
        ],
        'provider' => 'amazon_bwp',
        'sent_at'  => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/amazon_bwp',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
