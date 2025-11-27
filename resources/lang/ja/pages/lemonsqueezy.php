<?php

return [

    'hint' => 'HMAC 署名ヘッダー。',

    'summary' => <<<'TEXT'
Lemon Squeezy は各 webhook を **raw request body** に対するシンプルな HMAC で署名します。送信側は webhook の “signing secret” を用いて SHA-256 HMAC の **hex digest** を生成し、その digest を `X-Signature` ヘッダーに入れて送ります。あなたの役割は、受信したボディのバイト列をそのまま読み取り（再 stringify しない／空白を変えない）、同じ secret で HMAC を計算し、**hex** 文字列として出力し、`X-Signature` と constant-time（timing-safe）比較で照合することです。値が一致しない、またはヘッダーが存在しない場合は、ビジネスロジックに入る前にリクエストを拒否してください。

フレームワークのデフォルト設定では、ハッシュ計算前に body をパースしてしまうことが多いので、ルートで raw bytes にアクセスできるよう構成してください（例: Node/Express で “raw body” を有効化）。検証はゲートとして扱い、成功してから JSON をパースして状態を更新します。リトライや重複で副作用が二重適用されないよう handler を idempotent にし、secret ではなく最小限の診断情報（受信ヘッダー長、検証結果、event id）だけを記録します。ローカルテストでは Lemon Squeezy の test events を使い、失敗もシミュレートして retry/backoff の挙動を確認しましょう。検証・重複排除・非同期処理までの一連の流れをドキュメント化し、環境差があってもチームで同じ結果を再現できるようにします。
TEXT
    ,

    // dari view Lemon Squeezy
    'docs' => 'https://docs.lemonsqueezy.com/help/webhooks/signing-requests',

    'signature_notes' => [
        '`X-Signature`（raw body の HMAC-SHA256 **hex**）を読み取り、raw request bytes を取得します。',
        'signing secret で hex HMAC を計算し、timing-safe 関数で比較します。',
        '不一致/ヘッダー欠落なら拒否。検証成功後にのみ JSON をパースします。',
        'フレームワークが raw body を提供するようにし（再シリアライズしない）、handler を idempotent にして最小限の診断情報だけをログに残します。',
    ],

    'example_payload' => [
        'meta'     => ['event_name' => 'order_created'],
        'data'     => ['id' => 'ord_001', 'status' => 'paid'],
        'provider' => 'lemonsqueezy',
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
            'path'   => '/api/webhooks/lemonsqueezy',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
