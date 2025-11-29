<?php

return [

    'hint' => '공개키 서명(Classic) / 시크릿(Billing).',

    'summary' => <<<'TEXT'
Paddle Billing은 모든 웹훅을 `Paddle-Signature` 헤더로 서명하며, 이 헤더에는 Unix 타임스탬프(`ts`)와 서명값(`h1`)이 포함됩니다. 수동으로 검증하려면 `ts` + 콜론(`:`) + “수신한 raw request body 그대로”를 이어 붙여 signed payload를 만들고, notification destination의 시크릿 키로 HMAC-SHA256을 계산한 뒤 `h1`과 constant-time(타이밍 안전) 비교로 대조합니다. Paddle은 notification destination마다 별도의 시크릿을 발급하니, 비밀번호처럼 취급하고 소스에 포함하지 마세요.

공식 SDK 또는 자체 검증 미들웨어를 사용해 파싱 전에 반드시 검증하세요. 타이밍/바디 변환이 흔한 함정이므로, 프레임워크에서 raw bytes를 노출하도록 설정하고(예: Express `express.raw({ type: 'application/json' })`), 리플레이 방지를 위해 `ts` 허용 오차를 짧게 적용하세요. 검증 후에는 빠르게 2xx로 ACK하고, idempotency를 위해 event ID를 저장하며, 무거운 작업은 백그라운드 잡으로 넘기세요. 이렇게 하면 전달 안정성이 높아지고 재시도 상황에서 중복 부작용을 줄일 수 있습니다.

Paddle Classic에서 마이그레이션하는 경우, 검증 방식이 공개키 기반 서명에서 Billing의 시크릿 기반 HMAC으로 바뀌었습니다. 이에 맞춰 runbook과 시크릿 관리를 업데이트하고, 변경 롤아웃 시 검증 지표를 모니터링하세요. 시크릿이 포함되지 않은 명확한 로그와 결정적인 에러 응답은 장애 대응과 파트너 지원을 크게 단순화합니다.
TEXT
    ,

    // Dari view Paddle
    'docs' => 'https://developer.paddle.com/webhooks/signature-verification',

    'signature_notes' => [
        '`Paddle-Signature` 헤더를 읽고 `ts`, `h1` 값을 파싱합니다.',
        'signed payload = `ts + ":" + <raw request body>` 를 만들고, 엔드포인트 시크릿 키로 해시(HMAC)를 계산합니다.',
        '계산한 값과 `h1`을 timing-safe 함수로 비교하고, 리플레이 방지를 위해 `ts` 허용 오차를 짧게 적용합니다.',
        '공식 SDK 또는 검증 미들웨어를 우선 사용하고, 검증 성공 후에만 JSON을 파싱합니다.',
    ],

    'example_payload' => [
        'event_id' => 'evt_'.now()->timestamp,
        'event_type' => 'transaction.completed',
        'provider' => 'paddle',
        'data' => [
            'transaction_id' => 'txn_001',
            'amount' => 25000,
            'currency_code' => 'IDR',
            'status' => 'completed',
        ],
        'occurred_at' => now()->toIso8601String(),
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
            'path' => '/api/webhooks/paddle',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
