<?php

return [

    'hint' => 'プロダクト固有の通知。',

    'summary' => <<<'TEXT'
Payoneer Checkout は非同期通知（webhook）を、あなたが管理するエンドポイントへ送信し、ユーザーのブラウザ外で安全に支払い状態を照合できるようにします。プラットフォームでは通知先 URL を専用に定義でき、スタックに合わせて配信方式を選べます—POST（推奨）または GET、JSON もしくはフォームエンコードされたパラメータ。パラメータの集合や署名/認証パターンはプロダクトごとに異なるため、Payoneer の通知は「統合面」として扱ってください。イベントを識別するヘッダー/フィールドを文書化し、利用可能なら anti-replay のメタデータを含め、状態を変更する前に真正性を検証します。

運用面では、まず小さく idempotent なハンドラに切り出し、不変（immutable）なイベント記録を永続化してから素早く 2xx を返します。リトライ嵐を避けるため、重いビジネス処理はバックグラウンドワーカーへ委譲します。重複排除キーを適用し、timestamp/nonce がある場合は freshness ウィンドウを強制して、リプレイや順不同配信に備えます。追加の保証が必要なら、通知 URL にプロバイダ発行のトークン（または自前のランダムシークレット）を付与し、サーバー側で検証してください。最後に、チーム向けの runbook（エンドポイント、形式、失敗コード、Payoneer のプロダクトバリアントに対して実装した検証手順）を整備し、コードと一緒にバージョン管理します。
TEXT
    ,

    // Dari view Payoneer
    'docs' => 'https://checkoutdocs.payoneer.com/docs/create-notification-endpoints',

    'signature_notes' => [
        '専用の通知エンドポイントを用意（POST 推奨）。JSON またはフォームデータを受け付けます。',
        'プロダクトバリアントのドキュメントに従って真正性を検証（トークンまたは署名フィールド）。不一致なら拒否します。',
        'timestamp/nonce が利用可能なら freshness を強制し、処理を idempotent に（dedup key を保存）。',
        '素早く ACK（2xx）し、重い処理はバックグラウンドへオフロード。secret をログせず監査用の履歴を残します。',
    ],

    'example_payload' => [
        'event' => 'checkout.transaction.completed',
        'provider' => 'payoneer',
        'data' => [
            'orderId' => 'PO-001',
            'amount' => 25000,
            'currency' => 'IDR',
            'status' => 'COMPLETED',
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
            'path' => '/api/webhooks/payoneer',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
