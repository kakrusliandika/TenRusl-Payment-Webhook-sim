<?php

return [

    'hint' => 'Webhook 서명 검증 API.',

    'summary' => <<<'TEXT'
PayPal은 공식 Verify Webhook Signature API를 통해 각 webhook을 서버 사이드에서 검증하도록 요구합니다. 리스너는 알림과 함께 전달되는 헤더—`PAYPAL-TRANSMISSION-ID`, `PAYPAL-TRANSMISSION-TIME`, `PAYPAL-CERT-URL`, `PAYPAL-TRANSMISSION-SIG`—와 함께, 본인의 `webhook_id`, 그리고 **raw** 요청 바디(`webhook_event`)를 추출해야 합니다. 이 값들을 검증 엔드포인트로 전송하고, PayPal이 성공 결과를 반환할 때만 이벤트를 수락하세요. 이는 이전 검증 메커니즘을 대체하며, REST 제품 전반에서 일관성을 높입니다.

컨슈머는 빠르고 idempotent한 게이트로 구성하세요: 먼저 검증하고, 이벤트 레코드를 저장한 뒤, 2xx로 응답하고, 무거운 작업은 큐로 넘깁니다. 로컬 체크가 필요하다면 constant-time 비교를 사용하고, PayPal로 전달할 때 raw bytes를 그대로 유지해 미묘한 재직렬화 버그를 피하세요. `PAYPAL-TRANSMISSION-TIME` 주변에 짧은 시간 허용 오차를 적용해 replay 윈도우를 줄이고, 최소한의 감사 로그(요청 ID, 검증 결과, 바디 해시—secret 제외)만 남기세요. 이 패턴이면 중복 전달이나 부분 장애에서도 이중 처리가 발생하지 않고, 인시던트 대응 시 감사 추적도 신뢰할 수 있습니다.
TEXT
    ,

    // Dari view PayPal
    'docs' => 'https://developer.paypal.com/docs/api/webhooks/v1/',

    'signature_notes' => [
        '헤더를 수집: PAYPAL-TRANSMISSION-ID, PAYPAL-TRANSMISSION-TIME, PAYPAL-CERT-URL, PAYPAL-TRANSMISSION-SIG; raw body는 그대로 보관.',
        '위 값들과 webhook_id, webhook_event를 포함해 Verify Webhook Signature API를 호출하고, 성공일 때만 수락.',
        '검증을 게이트로 취급; replay 완화를 위해 짧은 시간 허용을 적용하고 컨슈머를 idempotent하게 구성.',
        '2xx를 빠르게 반환하고 무거운 작업은 큐로; 최소 진단만 로그(비밀정보 없음).',
    ],

    'example_payload' => [
        'id' => 'WH-'.now()->timestamp,
        'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
        'resource' => [
            'id' => 'CAP-001',
            'status' => 'COMPLETED',
        ],
        'provider' => 'paypal',
        'create_time' => now()->toIso8601String(),
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
            'path' => '/api/webhooks/paypal',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
