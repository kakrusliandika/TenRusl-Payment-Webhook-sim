<?php

return [

    'hint' => 'Client-Id/Request-* ヘッダーによる署名。',

    'summary' => <<<'TEXT'
DOKU は HTTP 通知を、ヘッダー主導のカノニカル（canonical）署名で保護しています。payload に対して何か処理を行う前に、必ずこの署名を検証してください。各コールバックには `Signature` ヘッダーが付与され、その値は `HMACSHA256=<base64>` の形式です。期待値を再構成するには、まずリクエストボディの `Digest` を計算します。受信した raw JSON バイト列に対する SHA-256 を取り、それを base64 エンコードします。次に、改行区切りの文字列を、以下 5 要素をこの順序・表記のまま並べて作成します:
  • `Client-Id:<value>`
  • `Request-Id:<value>`
  • `Request-Timestamp:<value>`
  • `Request-Target:<path-of-your-notification-url>`（例: `/payments/notifications`）
  • `Digest:<base64-of-SHA256(body)>`
その後、このカノニカル文字列に対して DOKU Secret Key を鍵として SHA-256 の HMAC を計算し、結果を base64 エンコードしたうえで `HMACSHA256=` を先頭に付与します。最後に、`Signature` ヘッダーと constant-time 比較で照合してください。不一致、要素欠落、値の不正はすべて認証失敗として扱い、リクエストを即時に拒否する必要があります。

耐障害性と安全性のため、正しい通知は素早く（2xx）応答し、重い処理はバックグラウンドジョブへ退避してリトライを誘発しないようにします。処理済みの識別子（例: `Request-Id` またはボディ内のイベント ID）を記録して、コンシューマを idempotent にしてください。freshness の検証も重要です。`Request-Timestamp` はリプレイ攻撃を防ぐため厳しい時間窓内であるべきで、`Request-Target` が実際のルートと一致していることも確認して canonicalization の不整合を防ぎます。パース時は DOKU の推奨どおり厳格すぎない方針で、未知フィールドは無視し、壊れやすいパーサよりスキーマ進化を優先しましょう。インシデント対応では、必須ヘッダーの有無、計算した digest/signature（secret は絶対にログしない）、およびボディのハッシュを記録し、機密情報を漏らさず監査に役立てます。
TEXT
    ,

    // dari view DOKU
    'docs' => 'https://developers.doku.com/get-started-with-doku-api/signature-component/non-snap/signature-component-from-request-header',

    'signature_notes' => [
        'ヘッダー `Client-Id`、`Request-Id`、`Request-Timestamp`、`Signature` を読み取り、`Request-Target`（自ルートのパス）を推定します。',
        '`Digest = base64( SHA256(raw JSON body) )` を計算します。',
        'Client-Id / Request-Id / Request-Timestamp / Request-Target / Digest の順に、各行 1 要素でカノニカル文字列を作ります（末尾に改行を付けない）。',
        'expected = `HMACSHA256=base64( HMAC-SHA256(canonical, SecretKey) )` を計算し、`Signature` と constant-time 比較します。',
        'timestamp の freshness を強制し、処理を idempotent にし、素早く ACK（2xx）して重い処理はオフロードします。',
    ],

    'example_payload' => [
        'order' => ['invoice_number' => 'INV-001'],
        'transaction' => ['status' => 'SUCCESS'],
        'provider' => 'doku',
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
            'path' => '/api/webhooks/doku',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
