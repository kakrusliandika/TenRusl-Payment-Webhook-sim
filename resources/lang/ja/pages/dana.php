<?php

return [

    'hint' => 'RSA（DANA の公開鍵で検証）。',

    'summary' => <<<'TEXT'
DANA は **非対称** 署名方式を採用しています。リクエストは秘密鍵で署名され、インテグレーターは公式の **DANA 公開鍵** を用いて検証します。実装としては、Webhook ヘッダー（例：`X-SIGNATURE`）から署名を取得し、base64 デコードしたうえで、RSA-2048 + SHA-256 を使って受信した HTTP 生ボディ（raw body）をその署名と照合します。検証が成功した場合にのみ、payload を真正なものとして扱ってください。検証に失敗した場合、または署名/ヘッダーが存在しない場合は、non-2xx を返して処理を停止します。

Webhook はリトライされたり順序が前後して届くことがあるため、ハンドラーは idempotent に設計します。ユニークなイベント ID を永続化して重複をショートサーキットし、timestamp/nonce がある場合は鮮度を確認してリプレイを抑止し、署名検証が通るまではすべてのフィールドを信用しません。検証前に JSON を再シリアライズしないでください。ワイヤ上で受け取ったバイト列そのままを対象に検証/ハッシュします。シークレットや秘密鍵はログに残さず、必要なら高レベルの診断情報（検証結果、ボディのハッシュ、イベント ID）のみに絞り、保存時も安全に保護します。

チーム向けには短い運用手順（runbook）を用意しましょう。DANA 公開鍵の読み込み/ローテーション方法、各言語/ランタイムでの検証方法、統合における string-to-sign の厳密なルール、恒久エラーと一時エラーの区別を含めます。さらに、堅牢な retry/backoff 方針、制限付きワークキュー、ヘルスチェック、検証失敗のアラートを組み合わせます。これにより、高負荷でも安全で、リトライに強く、DANA が要求する暗号学的検証に準拠した Webhook コンシューマを実現できます。
TEXT
    ,

    // dari view DANA
    'docs' => 'https://dashboard.dana.id/api-docs-v2/guide/authentication',

    'signature_notes' => [
        '`X-SIGNATURE` ヘッダーの値を base64 デコードします。',
        '公式の DANA 公開鍵を使用し、受信した raw HTTP body そのものに対して RSA-2048 + SHA-256 を検証します。検証が成功した場合のみ受け入れます。',
        '署名が欠落/無効、または payload が不正な Webhook は拒否します。検証成功前のデータは決して信用しません。',
    ],

    'example_payload' => [
        'id' => 'evt_'.now()->timestamp,
        'type' => 'payment.paid',
        'provider' => 'dana',
        'data' => [
            'transaction_id' => 'DANA-001',
            'amount' => 25000,
            'currency' => 'IDR',
            'status' => 'SUCCESS',
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
            'path' => '/api/webhooks/dana',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
