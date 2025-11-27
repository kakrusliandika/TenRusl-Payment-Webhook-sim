<?php

return [

    'hint' => 'x-timestamp ＋ body に対する HMAC-SHA256。',

    'summary' => <<<'TEXT'
Airwallex の Webhook には署名が付与されており、データベースに触る前に真正性と完全性を検証できます。各リクエストには重要なヘッダーが 2 つあります：`x-timestamp` と `x-signature` です。メッセージを検証するには、まず受信した HTTP ボディを生データのまま読み取り、そのボディに `x-timestamp` の値（文字列）を連結してダイジェストの入力を作ります。次に、その値に対して SHA-256 を用いた HMAC を計算し、キーとして通知 URL 用に共有されているシークレットを使います。Airwallex は結果を **16 進ダイジェスト（hex digest）** として想定しているため、その値を `x-signature` ヘッダーと、タイミング情報が漏れないよう constant-time な方法で比較します。署名が一致しない場合や、タイムスタンプが欠落・不正な場合は、安全側に倒して処理を失敗させ、2xx 以外のレスポンスを返してください。

あらゆる Webhook システムにおいて replay 攻撃は現実的なリスクなので、`x-timestamp` に対して「新鮮さ」の時間窓を設けるべきです。古すぎる、あるいは未来すぎるタイムスタンプのメッセージは拒否し、すでに処理したイベント ID を保存して、後続処理での副作用を重複排除します（アプリケーション層での idempotency）。検証が完了するまでは payload を信頼しないでください。ハッシュを計算する前に JSON を再シリアライズせず、到着したときの生バイト列をそのまま使うことで、空白や順序の微妙な差異による不整合を防ぎます。検証に成功したら、すぐに `2xx` を返し、重い処理は非同期で行ってリトライロジックを健全に保ち、重複発生を減らしましょう。

ローカルおよび CI フロー向けに、Airwallex は優れたツール群を提供しています。ダッシュボードで通知 URL を設定し、サンプル payload をプレビューし、エンドポイントに対して**テストイベントを送信**できます。デバッグ時には、受信した `x-timestamp`、計算した署名のプレビュー（シークレットは絶対にログしない）、そして存在する場合はイベント ID をログ出力するとよいでしょう。シークレットキーをローテーションする場合は、安全に切り替えを行い、署名エラー率を監視してください。最後に、検証・重複排除・リトライ・エラーレスポンスという一連の流れをドキュメント化しておくことで、チームメイトが同じ raw-body のハッシュルールと時間窓を使って結果を再現できるようにします。
TEXT
    ,

    'docs' => 'https://www.airwallex.com/docs/developer-tools/webhooks/listen-for-webhook-events',

    'signature_notes' => [
        '`x-timestamp` と `x-signature` をヘッダーから取り出します。',
        'value_to_digest = <x-timestamp> + <生の HTTP ボディ>（バイト列そのまま）を構築します。',
        'expected = HMAC-SHA256(value_to_digest, <webhook secret>) を HEX 文字列で計算し、`x-signature` と constant-time な比較で照合します。',
        '署名が一致しない、またはタイムスタンプが古い場合は拒否します。また、処理済みのイベント ID を重複排除して idempotency を保証します。',
    ],

    'example_payload' => [
        'id'       => 'evt_' . now()->timestamp,
        'type'     => 'payment_intent.succeeded',
        'data'     => [
            'payment_intent_id' => 'pi_awx_001',
            'amount'            => 25000,
            'currency'          => 'IDR',
            'status'            => 'succeeded',
        ],
        'provider'   => 'airwallex',
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
            'path'   => '/api/webhooks/airwallex',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
