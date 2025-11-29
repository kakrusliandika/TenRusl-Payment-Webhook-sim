<?php

return [

    'hint' => 'X-Callback-Signature.',

    'summary' => <<<'TEXT'
TriPay は設定した URL に対してコールバックを送信し、イベントを識別し送信元の認証に役立つヘッダーを含めます。特に、コールバックには `X-Callback-Event`（例: `payment_status`）と、TriPay のドキュメントに従った署名検証のための `X-Callback-Signature` が含まれます。コンシューマーはこれらのヘッダーを読み取り、リクエストの真正性を検証した上で、内部状態を更新してください。

エンドポイントは高速かつ冪等（idempotent）に設計します。timestamp/nonce がある場合は短い freshness window を適用し、reference や event 識別子をキーにした軽量な重複排除ストアを維持します。イベントを記録したら迅速に 2xx を返し、副作用は非同期で処理します。透明性とインシデント対応のため、受信時刻・イベントメタデータ・検証結果を記録する監査ログ（secret は記録しない）を保持します。
TEXT
    ,

    // dari view Tripay
    'docs' => 'https://tripay.co.id/developer',

    'signature_notes' => [
        '`X-Callback-Event`（例: `payment_status`）と `X-Callback-Signature` を確認します。',
        'TriPay のドキュメントに従って署名を検証し、不一致またはヘッダー欠落の場合は拒否します。',
        '処理は冪等に保ち（reference/event ID で重複排除）、迅速に 2xx で応答します。',
    ],

    'example_payload' => [
        'reference' => 'TRX-001',
        'status' => 'PAID',
        'amount' => 125000,
        'currency' => 'IDR',
        'provider' => 'tripay',
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
            'path' => '/api/webhooks/tripay',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
