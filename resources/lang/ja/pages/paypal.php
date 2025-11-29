<?php

return [

    'hint' => 'Verify Webhook Signature API。',

    'summary' => <<<'TEXT'
PayPal は、公式の Verify Webhook Signature API を用いた各 webhook のサーバーサイド検証を要求します。リスナーは通知に含まれるヘッダー—`PAYPAL-TRANSMISSION-ID`、`PAYPAL-TRANSMISSION-TIME`、`PAYPAL-CERT-URL`、`PAYPAL-TRANSMISSION-SIG`—に加えて、あなたの `webhook_id` と **raw** リクエストボディ（`webhook_event`）を抽出します。これらを検証エンドポイントへ送信し、PayPal が成功結果を返した場合にのみイベントを受け入れてください。これは旧来の検証方式を置き換え、REST 製品間の整合性を取りやすくします。

コンシューマは高速で idempotent なゲートとして設計します。まず検証し、イベント記録を永続化し、2xx で即時応答し、重い処理はキューへ送ります。ローカルチェックには constant-time 比較を使い、PayPal へフォワードする際も raw bytes を保持して再シリアライズ起因の微妙な不具合を避けます。`PAYPAL-TRANSMISSION-TIME` には厳密な許容範囲を設けてリプレイ窓を縮小し、最小限の監査ログ（request ID、検証結果、body ハッシュ—secret は除外）に留めてください。このパターンなら、重複配信や部分障害でも二重処理を防ぎ、インシデント対応時の監査証跡も信頼できます。
TEXT
    ,

    // Dari view PayPal
    'docs' => 'https://developer.paypal.com/docs/api/webhooks/v1/',

    'signature_notes' => [
        'ヘッダーを収集: PAYPAL-TRANSMISSION-ID, PAYPAL-TRANSMISSION-TIME, PAYPAL-CERT-URL, PAYPAL-TRANSMISSION-SIG。raw body を保持します。',
        'これらの値に webhook_id と webhook_event を加えて Verify Webhook Signature API を呼び出し、成功時のみ受理します。',
        '検証をゲートとして扱い、リプレイ対策に短い許容範囲を適用し、処理を idempotent にします。',
        '2xx を速やかに返し、重い処理はキューへ。診断ログは最小限（secret なし）にします。',
    ],

    'example_payload' => [
        'id' => 'WH-'.now()->timestamp,
        'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
        'resource' => [
            'id' => 'CAP-001',
            'status' => 'COMPLETED',
        ],
        'provider' => 'paypal',
        'create_time' => now()->toIso8601String(),
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
            'path' => '/api/webhooks/paypal',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
