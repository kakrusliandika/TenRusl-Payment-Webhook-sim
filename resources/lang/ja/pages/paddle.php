<?php

return [

    'hint' => '公開鍵署名（Classic）/ シークレット（Billing）。',

    'summary' => <<<'TEXT'
Paddle Billing はすべての webhook を `Paddle-Signature` ヘッダーで署名します。このヘッダーには Unix タイムスタンプ（`ts`）と署名（`h1`）が含まれます。手動で検証する場合は、`ts` とコロン（`:`）と「受信した raw request body（そのまま）」を連結して signed payload を作成し、通知先（notification destination）のシークレットキーで HMAC-SHA256 を計算します。得られた値を `h1` と constant-time（タイミング安全）比較で照合してください。Paddle は通知先ごとに別のシークレットを発行します。パスワード同様に扱い、ソース管理に入れないでください。

公式 SDK もしくは自前の検証ミドルウェアで、パース前に必ず検証しましょう。タイミングや body 変換はよくある落とし穴なので、フレームワークで raw bytes を取得できるようにし（例: Express `express.raw({ type: 'application/json' })`）、リプレイ対策として `ts` の許容範囲を短く設定します。検証後はすぐに 2xx を返し、idempotency のため event ID を保存し、重い処理はバックグラウンドジョブに回します。これにより配信が安定し、リトライ時の重複副作用を防げます。

Paddle Classic から移行する場合、検証方式は公開鍵署名から Billing のシークレットベース HMAC に移行しています。runbook とシークレット管理を更新し、段階的な反映時には検証メトリクスを監視してください。シークレットを含まない明確なログと、決定的なエラーレスポンスは障害対応とパートナーサポートを大幅に楽にします。
TEXT
    ,

    // Dari view Paddle
    'docs' => 'https://developer.paddle.com/webhooks/signature-verification',

    'signature_notes' => [
        '`Paddle-Signature` ヘッダーを読み取り、`ts` と `h1` を抽出します。',
        'signed payload = `ts + ":" + <raw request body>` を作成し、エンドポイントのシークレットでハッシュします。',
        '`h1` と timing-safe 関数で比較し、リプレイ防止のため `ts` の許容範囲を短く設定します。',
        '公式 SDK または検証ミドルウェアを優先し、検証成功後にのみ JSON をパースします。',
    ],

    'example_payload' => [
        'event_id'   => 'evt_' . now()->timestamp,
        'event_type' => 'transaction.completed',
        'provider'   => 'paddle',
        'data'       => [
            'transaction_id' => 'txn_001',
            'amount'         => 25000,
            'currency_code'  => 'IDR',
            'status'         => 'completed',
        ],
        'occurred_at' => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/paddle',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
