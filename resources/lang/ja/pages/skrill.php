<?php

return [

    'hint' => 'MD5/HMAC 風のコールバック署名。',

    'summary' => <<<'TEXT'
Skrill は `status_url` にステータスコールバックを POST し、`md5sig` を用いてメッセージを検証することを求めます。`md5sig` は、定義済みのフィールド連結（例: `merchant_id + transaction_id + UPPERCASE(MD5(secret_word)) + mb_amount + mb_currency + status`）に対して計算した **大文字（UPPERCASE）の MD5** です。計算した値が受信した `md5sig` と一致する場合にのみ payload を信頼してください。Skrill は要望により、`md5sig` と同様の作り方で構成される代替の `sha2sig`（UPPERCASE SHA-2）も提供します。

実運用では、署名検証は必ずバックエンドで行い（secret word を外部に出さない）、コールバックで送られてきた **そのままの値** を使ってハッシュしてください。エンドポイントは idempotent にし（transaction もしくは event ID で重複排除）、永続化後は速やかに 2xx を返し、非クリティカルな処理は後段に回します。デバッグ時は検証結果と body ハッシュを記録しつつ、secret はログに残さないでください。フォーマットにも注意し、amount や currency は署名文字列を作る際に verbatim（改変なし）で扱うことで、リトライや環境差でも比較が安定します。
TEXT
    ,

    // dari view Skrill
    'docs' => 'https://www.skrill.com/fileadmin/content/pdf/Skrill_Quick_Checkout_Guide_v10.3.pdf',

    'signature_notes' => [
        '`md5sig` を正確に再構築: ドキュメントのフィールド（例: merchant_id, transaction_id, UPPERCASE(MD5(secret_word)), mb_amount, mb_currency, status）を連結し、**UPPERCASE MD5** を計算。',
        '受信した `md5sig` と比較。Skrill 側で有効化されていれば `sha2sig`（UPPERCASE SHA-2）も利用可能。',
        '検証はサーバー側のみで実施し、投稿された値をそのまま使用。ハンドラは idempotent にし、2xx を素早く返す。',
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
