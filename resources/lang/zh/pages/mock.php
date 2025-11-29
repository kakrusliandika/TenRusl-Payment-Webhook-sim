<?php

return [

    'hint' => '快速测试。',

    'summary' => <<<'TEXT'
这个 mock provider 是一个确定性（deterministic）、无需任何凭证的练习场，用来演练完整的 webhook 生命周期：请求创建、幂等状态流转、投递、校验、重试与失败处理。由于没有外部依赖，你可以在本地或 CI 中快速迭代、记录 fixtures，并在不泄露真实 secret 的前提下展示架构决策（例如把校验放在持久化之前还是之后）。

它也适合模拟常见故障模式：延迟投递、重复发送、乱序事件，以及触发指数退避（exponential backoff）的临时 5xx 响应。mock 还支持不同“签名模式”（none / HMAC-SHA256 / RSA-verify stub），便于团队在安全环境中练习 raw-body 哈希、constant-time 比较与时间戳窗口。这样你就能在接入真实网关前先验证幂等键与去重表的设计。

为了提升文档与演示质量，让 mock 尽量贴近生产：端点形态、请求头与错误码保持一致；区别只在信任根来源。对有效的 webhook 要快速 ACK（2xx），并把重任务下放到后台作业。在校验通过之前，一律将 mock 的 payload 视为不可信——通过后再执行业务规则。最终你会得到快速反馈链路，以及一个可移植的 demo，能够镜像你最终要上线的架构。
TEXT
    ,

    // dari view mock
    'docs' => null,

    'signature_notes' => [
        '模拟器模式：none / HMAC-SHA256 / RSA-verify stub；通过配置选择，用于练习不同验签路径。',
        '对收到的 raw request body 原样哈希；使用 timing-safe 函数比较；强制短的 replay 窗口。',
        '记录已处理的事件 ID 以保证幂等；对有效 webhook 快速 ACK（2xx），并延后/下放重任务。',
    ],

    'example_payload' => [
        'id' => 'evt_'.now()->timestamp,
        'type' => 'payment.succeeded',
        'provider' => 'mock',
        'data' => [
            'payment_id' => 'pay_mock_001',
            'amount' => 25000,
            'currency' => 'IDR',
            'status' => 'succeeded',
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
            'path' => '/api/webhooks/mock',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
