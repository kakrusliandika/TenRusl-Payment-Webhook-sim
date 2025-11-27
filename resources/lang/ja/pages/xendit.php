<?php

return [

    'hint' => 'コールバックトークン署名。',

    'summary' => <<<'TEXT'
Xendit は webhook イベントをアカウント単位のトークンで署名し、そのトークンを `x-callback-token` ヘッダーで送信します。実装側はこのヘッダーを Xendit ダッシュボードで取得したトークンと照合し、ヘッダー欠落または不一致のリクエストは拒否する必要があります。Webhook 製品によっては `webhook-id` も含まれ、リトライ時の重複処理を防ぐために保存して活用できます。

運用面では、検証を最初のステップにし、変更不能なイベントレコードを永続化してから速やかに 2xx で応答し、重い処理はキューへ移します。`webhook-id`（または独自キー）で冪等性を担保し、timestamp メタデータがある場合は厳密な時間ウィンドウも適用します。検証・重複排除・リトライ・エラーコードまで含めた一連の流れをドキュメント化し、チームとサービスが環境間で一貫して統合できるようにします。
TEXT
    ,

    // dari view Xendit
    'docs' => 'https://docs.xendit.co/docs/handling-webhooks',

    'signature_notes' => [
        '`x-callback-token` をダッシュボードの固有トークンと比較し、不一致なら拒否します。',
        '`webhook-id`（存在する場合）で重複排除します。JSON を解析する前に検証をハードゲートとして扱います。',
        '2xx を素早く返し重い処理は後段へ。secret を露出しない最小限の診断ログに留めます。',
    ],

    'example_payload' => [
        'id'       => 'evt_xnd_' . now()->timestamp,
        'event'    => 'invoice.paid',
        'data'     => [
            'id'     => 'inv_001',
            'status' => 'PAID',
        ],
        'provider' => 'xendit',
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
            'path'   => '/api/webhooks/xendit',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
