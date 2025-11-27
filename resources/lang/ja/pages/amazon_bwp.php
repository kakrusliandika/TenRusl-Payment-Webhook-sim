<?php

return [

    'hint' => '各リクエストに付与される x-amzn-signature。',

    'summary' => <<<'TEXT'
Buy with Prime（BWP）はすべての Webhook に署名を付与し、Amazon から本当に送信されたものか、転送中に改ざんされていないかを確認できるようにしています。各リクエストにはデジタル署名が `x-amzn-signature` ヘッダーとして含まれます。ハンドラ側では、対象のイベント種別と環境に対して BWP のドキュメントどおりに期待される署名を正確に再構築する必要があります。値が一致しない場合は、その呼び出しを拒否してください。リクエストに付随するタイムスタンプや nonce はリプレイ攻撃対策の一部として扱い、厳しい有効時間ウィンドウを設けるとともに、重複を避けるため処理済みの識別子を保存します。

運用面では、エンドポイントは高速かつ決定的（deterministic）になるよう設計します。まず検証を行い、安全に記録できたら `2xx` で応答し、重い処理は非同期で実行します。IP 制限（allowlist）に依存している場合でも、IP やネットワークは変化し得ることを忘れないでください—暗号学的な検証こそが主要な信頼の拠り所です。安全な監査ログ（リクエスト ID、署名の有無、検証結果、ボディのハッシュ—シークレットそのものではない）を残しましょう。ローカルテストでは、環境フラグの背後で検証ステップをスタブしても構いませんが、本番経路では必ず署名検証が行われるようにします。キーをローテーションしたり canonicalization ルールを更新したりする場合は慎重にロールフォワードし、エラー率を監視しながら、どのヘッダーセットとハッシュ/カノニカル化手順を実装しているかを正確にドキュメント化して、スタック内の他サービスと足並みを揃えてください。

統合のしやすさという観点では、**明確な失敗理由**（無効な署名、古いタイムスタンプ、リクエスト不正）を露出し、安定したエラーコードを返してリトライの挙動を予測しやすくすることが重要です。これをアプリケーション層での idempotency やリプレイ対策と組み合わせることで、リトライやバースト、部分的な障害が発生した場合でも、後続の決済状態の遷移を安全に保てます。
TEXT
    ,

    // Dari view amazon_bwp.blade.php
    'docs' => 'https://documents.buywithprime.amazon.com/bwp-api/docs/subscribe-to-events',

    'signature_notes' => [
        'リクエストヘッダーから `x-amzn-signature` を読み取ります。',
        'Buy with Prime で定義されているとおり（公式ドキュメントのアルゴリズム/カノニカル化）に期待される署名を再構築し、不一致の場合はリクエストを拒否します。',
        'timestamp/nonce が提供される場合は、リプレイ攻撃を軽減するため厳しいフレッシュネスウィンドウを適用し、処理済み ID を保存して重複を防ぎます。',
    ],

    'example_payload' => [
        'eventType' => 'ORDER_COMPLETED',
        'data'      => [
            'orderId'  => 'BWP-001',
            'status'   => 'COMPLETED',
            'amount'   => 25000,
            'currency' => 'IDR',
        ],
        'provider' => 'amazon_bwp',
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
            'path'   => '/api/webhooks/amazon_bwp',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
