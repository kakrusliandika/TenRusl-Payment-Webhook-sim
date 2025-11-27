<?php

return [

    'hint' => 'タイムスタンプ付き署名ヘッダー。',

    'summary' => <<<'TEXT'
Stripe はすべての webhook リクエストに署名し、計算済みの署名を `Stripe-Signature` ヘッダーに含めます。エンドポイントは処理を行う前に必ず検証してください。Stripe の公式ライブラリを使う場合、検証ルーチンに 3 つの入力を渡します：受信したままの raw リクエストボディ、`Stripe-Signature` ヘッダー、そしてエンドポイントシークレット。検証が成功した場合のみ処理を継続し、失敗した場合は non-2xx を返して処理を停止します。公式ライブラリが使えない場合は、ドキュメントに従って手動検証を実装し、リプレイリスクを下げるためのタイムスタンプ許容範囲チェックも含めてください。

署名検証は厳密なゲートとして扱います。ハンドラは冪等に保ち（イベント ID を保存）、永続化後は速やかに 2xx を返し、重い処理はバックグラウンドジョブへ移します。フレームワークが **raw bytes** を取得できることを確認し、ハッシュ前に JSON を再シリアライズしないでください（空白や順序の変化で検証が壊れます）。最後に、最小限の診断ログ（検証結果、イベント種別、ボディのハッシュ—シークレットは除外）だけを残し、シークレットのローテーションやエンドポイント変更時の失敗を監視します。
TEXT
    ,

    // dari view Stripe
    'docs' => 'https://docs.stripe.com/webhooks',

    'signature_notes' => [
        '`Stripe-Signature` ヘッダーを読み取り、Stripe ダッシュボードから endpoint secret を取得します。',
        '公式ライブラリで検証する場合は、raw body、`Stripe-Signature`、endpoint secret を渡します。',
        '手動検証では、リプレイ低減のためタイムスタンプ許容範囲を設け、timing-safe な比較で署名を照合します。',
        '成功時のみ受理し、冪等性のためイベント ID を保存、永続化後は速やかに 2xx を返します。',
    ],

    'example_payload' => [
        'id'   => 'evt_' . now()->timestamp,
        'type' => 'payment_intent.succeeded',
        'data' => [
            'object' => [
                'id'     => 'pi_01H',
                'status' => 'succeeded',
            ],
        ],
        'provider'   => 'stripe',
        'created_at' => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/stripe',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
