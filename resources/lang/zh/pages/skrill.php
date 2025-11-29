<?php

return [

    'hint' => 'MD5/HMAC 风格的回调签名。',

    'summary' => <<<'TEXT'
Skrill 会向你的 `status_url` POST 状态回调，并要求你使用 `md5sig` 验证消息。`md5sig` 是将一组定义好的字段按顺序拼接后（例如：`merchant_id + transaction_id + UPPERCASE(MD5(secret_word)) + mb_amount + mb_currency + status`）计算得到的**大写 MD5（uppercase MD5）**。只有当你计算出的值与传入的 `md5sig` 完全一致时，才应信任该 payload。Skrill 还支持按需启用的替代字段 `sha2sig`（大写 SHA-2），其构造方式与 `md5sig` 类似，只是哈希算法不同。

在实现上，务必在后端完成签名校验（绝不要暴露 secret word），并对回调中“原样提交”的参数值进行哈希。让端点保持幂等（按 transaction 或 event ID 去重），在持久化后尽快返回 2xx，将非关键工作延后处理。调试时可记录验签结果与 body 哈希，但不要把任何秘密写入日志。注意格式细节——金额与币种字段在构造签名字符串时必须逐字使用原值——这样在重试与不同环境下比较才稳定。
TEXT
    ,

    // dari view Skrill
    'docs' => 'https://www.skrill.com/fileadmin/content/pdf/Skrill_Quick_Checkout_Guide_v10.3.pdf',

    'signature_notes' => [
        '严格按文档重建 `md5sig`：拼接指定字段（如 merchant_id、transaction_id、UPPERCASE(MD5(secret_word))、mb_amount、mb_currency、status），并计算**大写 MD5**。',
        '与收到的 `md5sig` 比较；如 Skrill 已启用，也可使用 `sha2sig`（大写 SHA-2）作为替代。',
        '仅在服务端验证，并使用回传的原始值；处理器保持幂等并尽快返回 2xx。',
    ],

    'example_payload' => [
        'transaction_id' => 'SKR-001',
        'mb_amount' => '10.00',
        'mb_currency' => 'EUR',
        'status' => '2',
        'md5sig' => '<UPPERCASE_MD5>',
        'provider' => 'skrill',
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
            'path' => '/api/webhooks/skrill',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
