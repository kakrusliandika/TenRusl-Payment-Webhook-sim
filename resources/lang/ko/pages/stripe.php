<?php

return [

    'hint' => '타임스탬프가 포함된 서명 헤더.',

    'summary' => <<<'TEXT'
Stripe는 모든 webhook 요청에 서명하며, 계산된 서명을 `Stripe-Signature` 헤더로 제공합니다. 엔드포인트는 어떤 작업을 수행하기 전에 요청을 반드시 검증해야 합니다. Stripe 공식 라이브러리를 사용할 때는 검증 루틴에 3가지 입력을 전달합니다: 수신한 그대로의 raw request body, `Stripe-Signature` 헤더, 그리고 엔드포인트 시크릿(endpoint secret). 검증이 성공했을 때만 처리를 계속하고, 실패하면 non-2xx를 반환한 뒤 처리를 중단하세요. 공식 라이브러리를 사용할 수 없는 경우에는 문서대로 수동 검증을 구현하되, 재전송(replay) 위험을 줄이기 위해 타임스탬프 허용 오차(tolerance) 검사도 포함해야 합니다.

서명 검증을 엄격한 게이트로 취급하세요. 핸들러는 idempotent하게 유지하고(이벤트 ID 저장), 영속화 후 빠르게 2xx로 응답한 뒤, 무거운 작업은 백그라운드 잡으로 넘기세요. 프레임워크가 **raw bytes**를 제공하는지 확인하고, 해시 전에 JSON을 재직렬화하지 마세요. 공백이나 필드 순서가 바뀌면 서명 검증이 실패합니다. 마지막으로, 최소한의 진단 로그만 남기세요(검증 결과, 이벤트 타입, 바디 해시—비밀값 제외) 그리고 시크릿 로테이션이나 엔드포인트 변경 중 실패율을 모니터링하세요.
TEXT
    ,

    // dari view Stripe
    'docs' => 'https://docs.stripe.com/webhooks',

    'signature_notes' => [
        '`Stripe-Signature` 헤더를 읽고 Stripe 대시보드에서 endpoint secret을 확보합니다.',
        '공식 라이브러리로 검증할 때: raw request body, `Stripe-Signature`, endpoint secret을 전달합니다.',
        '수동 검증 시에는 replay를 줄이기 위해 timestamp tolerance를 적용하고, timing-safe 비교로 서명을 비교합니다.',
        '성공 시에만 수락하고, idempotency를 위해 이벤트 ID를 저장한 뒤 영속화 후 빠르게 2xx를 반환합니다.',
    ],

    'example_payload' => [
        'id' => 'evt_'.now()->timestamp,
        'type' => 'payment_intent.succeeded',
        'data' => [
            'object' => [
                'id' => 'pi_01H',
                'status' => 'succeeded',
            ],
        ],
        'provider' => 'stripe',
        'created_at' => now()->toIso8601String(),
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
            'path' => '/api/webhooks/stripe',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
