<?php

return [

    'hint' => '콜백 토큰 서명.',

    'summary' => <<<'TEXT'
Xendit는 계정별 토큰을 사용해 webhook 이벤트를 “서명”하며, 이 토큰은 `x-callback-token` 헤더로 전달됩니다. 통합 구현에서는 이 헤더 값을 Xendit 대시보드에서 발급받은 토큰과 비교해야 하며, 토큰이 누락되었거나 일치하지 않는 요청은 거부해야 합니다. 일부 webhook 제품은 `webhook-id`도 함께 제공하는데, 이를 저장해 두면 재시도(retry) 상황에서 중복 처리를 방지할 수 있습니다.

운영 관점에서는 검증을 항상 첫 단계로 두고, 변경 불가능한(immutable) 이벤트 레코드를 저장한 뒤, 즉시 2xx로 응답하고 무거운 작업은 큐로 넘기세요. `webhook-id`(또는 자체 키)로 idempotency를 보장하고, timestamp 메타데이터가 제공된다면 엄격한 시간 윈도우를 적용하세요. 검증, 중복 제거, 재시도, 에러 코드까지 전체 흐름을 문서화해 팀과 서비스가 환경별로 일관되게 통합할 수 있도록 하세요.
TEXT
    ,

    // dari view Xendit
    'docs' => 'https://docs.xendit.co/docs/handling-webhooks',

    'signature_notes' => [
        '`x-callback-token`을 Xendit 대시보드의 고유 토큰과 비교하고, 불일치 시 거부합니다.',
        '`webhook-id`가 있으면 중복 제거에 사용하며, JSON을 파싱하기 전에 검증을 하드 게이트로 취급합니다.',
        '2xx를 빠르게 반환하고 무거운 작업은 지연/위임하며, 비밀값 없이 최소한의 진단만 로깅합니다.',
    ],

    'example_payload' => [
        'id' => 'evt_xnd_'.now()->timestamp,
        'event' => 'invoice.paid',
        'data' => [
            'id' => 'inv_001',
            'status' => 'PAID',
        ],
        'provider' => 'xendit',
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
            'path' => '/api/webhooks/xendit',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
