<?php

return [

    'hint' => 'クイックテスト。',

    'summary' => <<<'TEXT'
この mock プロバイダは、決定的（deterministic）で認証情報不要のプレイグラウンドとして、Webhook のライフサイクル全体を練習できます。リクエスト生成、idempotent な状態遷移、配信、検証、リトライ、失敗処理まで一通り再現できます。外部依存がないため、ローカルや CI で高速に反復し、fixture を記録し、検証をどこに置くか／永続化をどこで行うかといったアーキテクチャ判断を、実際の secret を漏らさずに説明できます。

よくある障害パターンのシミュレーションにも使えます。配信遅延、重複送信、順不同イベント、そして指数バックオフ（exponential backoff）を誘発する一時的な 5xx 応答などです。さらに mock は “署名モード”（none / HMAC-SHA256 / RSA-verify stub）を用意しており、raw-body の hashing、constant-time 比較、timestamp の許容ウィンドウなどを安全に練習できます。これにより、実ゲートウェイ統合前に idempotency key や dedup テーブルの設計を検証できます。

ドキュメント品質のために、mock は本番に近づけましょう。エンドポイント形状、ヘッダー、エラーコードは同じにし、違いは trust root だけにします。有効な webhook は素早く ACK（2xx）し、重い処理はバックグラウンドジョブへオフロードします。検証が通るまで mock の payload は untrusted として扱い、通過後にビジネスルールを適用します。その結果、素早いフィードバックループと、出荷するアーキテクチャをそのまま反映したポータブルなデモが得られます。
TEXT
    ,

    // dari view mock
    'docs' => null,

    'signature_notes' => [
        'シミュレータのモード: none / HMAC-SHA256 / RSA-verify stub。設定で切り替えて検証パスを練習します。',
        '受信した raw request body をそのままハッシュし、timing-safe な関数で比較し、短いリプレイ許容ウィンドウを適用します。',
        'idempotency のため処理済み event ID を記録し、有効な webhook は高速に ACK（2xx）し、重い処理は後段へ回します。',
    ],

    'example_payload' => [
        'id'       => 'evt_' . now()->timestamp,
        'type'     => 'payment.succeeded',
        'provider' => 'mock',
        'data'     => [
            'payment_id' => 'pay_mock_001',
            'amount'     => 25000,
            'currency'   => 'IDR',
            'status'     => 'succeeded',
        ],
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
            'path'   => '/api/webhooks/mock',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
