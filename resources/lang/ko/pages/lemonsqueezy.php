<?php

return [

    'hint' => 'HMAC 서명 헤더.',

    'summary' => <<<'TEXT'
Lemon Squeezy는 모든 웹훅을 **raw request body**에 대해 단순한 HMAC으로 서명합니다. 발신자는 웹훅 “signing secret”을 사용해 SHA-256 HMAC **hex digest**를 생성하고, 그 digest를 `X-Signature` 헤더에 담아 전송합니다. 여러분의 작업은 수신한 body 바이트를 그대로(재-문자열화 금지, 공백/서식 변경 금지) 읽고, 동일한 secret으로 같은 HMAC을 계산해 **hex** 문자열로 출력한 뒤, `X-Signature`와 constant-time(타이밍 안전) 비교로 대조하는 것입니다. 값이 다르거나 헤더가 없으면 어떤 비즈니스 로직도 수행하기 전에 요청을 거부해야 합니다.

프레임워크 기본 동작 때문에 해시 계산 전에 body가 파싱되는 경우가 많으니, 라우트에서 raw bytes에 접근할 수 있도록 구성하세요(예: Node/Express에서 “raw body” 처리 설정). 검증을 게이트로 취급해, 통과한 뒤에만 JSON을 파싱하고 상태를 업데이트합니다. 재시도/중복으로 부작용이 두 번 적용되지 않도록 핸들러를 idempotent하게 만들고, secret 대신 최소한의 진단 정보(수신 헤더 길이, 검증 결과, 이벤트 ID)만 기록하세요. 로컬 테스트에서는 Lemon Squeezy의 테스트 이벤트를 사용하고 실패도 시뮬레이션해 retry/backoff 동작을 확인하세요. 검증, 중복 제거, 비동기 처리까지 end-to-end 경로를 문서화해 팀이 환경별로 일관된 결과를 재현할 수 있게 하세요.
TEXT
    ,

    // dari view Lemon Squeezy
    'docs' => 'https://docs.lemonsqueezy.com/help/webhooks/signing-requests',

    'signature_notes' => [
        '`X-Signature`(raw body의 HMAC-SHA256 **hex**)를 읽고 raw request bytes를 확보합니다.',
        'signing secret으로 hex HMAC을 계산한 뒤 timing-safe 함수로 비교합니다.',
        '불일치/헤더 누락 시 거부하고, 검증 성공 후에만 JSON을 파싱합니다.',
        '프레임워크가 raw body를 제공하도록 설정(재-직렬화 금지)하며, 핸들러를 idempotent하게 만들고 최소 진단만 로그로 남깁니다.',
    ],

    'example_payload' => [
        'meta' => ['event_name' => 'order_created'],
        'data' => ['id' => 'ord_001', 'status' => 'paid'],
        'provider' => 'lemonsqueezy',
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
            'path' => '/api/webhooks/lemonsqueezy',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
