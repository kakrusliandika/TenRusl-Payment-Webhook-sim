<?php

return [

    'hint' => 'プロバイダ固有のコールバック署名。',

    'summary' => <<<'TEXT'
OY! のコールバックは、登録済み API キーとパートナーリクエスト向けの送信元 IP の allowlisting を中心とした、より広いセキュリティ設計の一部です。さらに OY! には Authorization Callback 機能があり、コールバックがあなたのシステムへ届く前に制御・承認できるため、意図しない状態変更を防ぐ明示的なゲートを追加できます。とはいえ実運用では、検証が完了するまであらゆるコールバックを untrusted として扱い、freshness（timestamp/nonce のウィンドウ）を強制し、リトライや順不同配信でも安全に処理できるよう consumer を idempotent にしておくべきです。

公開プロバイダはコールバック署名方式がそれぞれ異なるため、当シミュレータでは強化されたベースラインとして、共有シークレットで raw request body をそのまま対象に計算する HMAC ヘッダー（例: `X-Callback-Signature`）を提示します。これは本番で使う原則と同じです：raw バイトのハッシュ化（再シリアライズしない）、constant-time 比較、短いリプレイ許容ウィンドウ。小さな dedup ストアと迅速な 2xx 応答を組み合わせ、プロバイダのリトライ動作を健全に保ちつつ重複副作用を避けます。

運用面では、監査ログ（受信時刻、検証結果、body のハッシュ—secret ではない）を保ち、secret を安全にローテーションし、検証失敗率を監視します。allowlist に依存する場合でも、それは変わり得ることを念頭に置き、暗号学的な検証（または OY! の明示的な認可ゲート）を主要な trust anchor として維持してください。エンドポイントは狭く、予測可能で、十分にドキュメント化し、他サービスやチームメンバーが安心して再利用できるようにします。
TEXT
    ,

    // dari view OY
    'docs' => 'https://docs.oyindonesia.com/',

    'signature_notes' => [
        'OY! のセキュリティ姿勢：登録済み API キー + パートナーリクエスト向け送信元 IP の allowlisting。',
        'Authorization Callback（ダッシュボード）を活用し、コールバックが到達する前に承認できるようにします。',
        '本シミュレータではベストプラクティス例として `X-Callback-Signature = HMAC-SHA256(raw_body, secret)` を検証します。constant-time 比較と freshness チェックも適用します。',
        '処理を idempotent にし、プロバイダのリトライを健全に保つため 2xx を速やかに返します。',
    ],

    'example_payload' => [
        'id'       => 'evt_' . now()->timestamp,
        'type'     => 'payment.completed',
        'provider' => 'oy',
        'data'     => [
            'partner_trx_id' => 'PRT-001',
            'amount'         => 25000,
            'currency'       => 'IDR',
            'status'         => 'COMPLETED',
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
            'path'   => '/api/webhooks/oy',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
