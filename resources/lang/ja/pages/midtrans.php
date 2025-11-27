<?php

return [

    'hint' => 'signature_key の検証。',

    'summary' => <<<'TEXT'
Midtrans は各 HTTP(S) 通知に計算済みの `signature_key` を含め、処理前に送信元を検証できるようにしています。式は明確で安定しています:
    SHA512(order_id + status_code + gross_amount + ServerKey)
通知ボディにある値（文字列としての値）と、あなたの秘密の `ServerKey` を使って入力文字列を組み立て、SHA-512 の hex digest を計算し、`signature_key` と constant-time 比較で照合してください。検証に失敗したら通知は破棄します。正当なメッセージでは、ドキュメントどおりのフィールド（例: `transaction_status`）を使って状態遷移を行い、素早く ACK（2xx）し、重い処理はキューへ、さらにリトライや順不同配信に備えて更新を idempotent にします。

よくある落とし穴は 2 つ：フォーマットと型変換です。文字列連結時の `gross_amount` は提供されたとおりの形式を厳密に維持し（ローカライズしない／小数を変えない）、トリムや改行・空白の変更も避けてください。レースコンディション対策として event 単位または order 単位の重複排除キーを保存し、監査用に検証結果とボディのハッシュをログ（secret は絶対に記録しない）に残します。さらに endpoint に rate limiting と明確な失敗コードを用意し、監視が一時的エラー（リトライ対象）と恒久的拒否（署名不正）を判別できるようにします。
TEXT
    ,

    // dari view midtrans
    'docs' => 'https://docs.midtrans.com/docs/https-notification-webhooks',

    'signature_notes' => [
        'ボディから `order_id`、`status_code`、`gross_amount`（文字列）を取り、`ServerKey` を連結します。',
        '`SHA512(order_id + status_code + gross_amount + ServerKey)` を計算し、`signature_key` と constant-time 比較します。',
        '不一致なら拒否。一致なら `transaction_status` に基づき状態更新。処理は idempotent にし、2xx を速やかに返します。',
        '連結時に `gross_amount` の形式変更や不要な空白が混ざらないよう注意します。',
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
