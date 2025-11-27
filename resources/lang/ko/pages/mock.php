<?php

return [

    'hint' => '빠른 테스트.',

    'summary' => <<<'TEXT'
이 mock provider는 결정적(deterministic)이며 크리덴셜이 필요 없는 놀이터로, 웹훅 라이프사이클 전체를 연습할 수 있습니다: 요청 생성, idempotent 상태 전이, 전달, 검증, 재시도(retry), 그리고 실패 처리. 외부 의존성이 없기 때문에 로컬이나 CI에서 빠르게 반복하고, fixture를 기록하며, 실제 secret을 노출하지 않고도(예: 검증을 어디에 두고 어디에서 저장할지) 아키텍처 결정을 시연할 수 있습니다.

일반적인 실패 모드를 시뮬레이션하는 데도 활용하세요: 지연 전달, 중복 전송, 순서가 뒤바뀐 이벤트, 그리고 지수 백오프(exponential backoff)를 유발하는 일시적 5xx 응답. mock은 서로 다른 “서명 모드”(none / HMAC-SHA256 / RSA-verify stub)도 지원하므로, 팀이 raw-body 해싱, constant-time 비교, 타임스탬프 윈도우를 안전한 환경에서 연습할 수 있습니다. 이를 통해 실제 게이트웨이를 붙이기 전에 idempotency 키와 dedup 테이블을 검증할 수 있습니다.

문서 품질을 위해 mock을 프로덕션에 가깝게 유지하세요: 동일한 엔드포인트 형태, 헤더, 에러 코드; 차이는 신뢰의 근원(trust root)뿐입니다. 유효한 웹훅은 빠르게 ACK(2xx)하고 무거운 작업은 백그라운드 잡으로 오프로딩하세요. 검증이 통과하기 전까지 mock의 payload는 신뢰하지 말고, 통과 후에 비즈니스 규칙을 적용하세요. 결과적으로 빠른 피드백 루프와, 실제로 출시할 아키텍처를 그대로 반영하는 휴대 가능한 데모를 얻을 수 있습니다.
TEXT
    ,

    // dari view mock
    'docs' => null,

    'signature_notes' => [
        '시뮬레이터 모드: none / HMAC-SHA256 / RSA-verify stub; 설정으로 선택해 검증 경로를 연습하세요.',
        '정확한 raw request body를 해시하고 timing-safe 함수로 비교하며, 짧은 리플레이 윈도우를 적용하세요.',
        'idempotency를 위해 처리된 이벤트 ID를 기록하고, 유효한 웹훅은 빠르게 ACK(2xx)하며 무거운 작업은 미루세요.',
    ],

    'example_payload' => [
        'id'       => 'evt_' . now()->timestamp,
        'type'     => 'payment.succeeded',
        'provider' => 'mock',
        'data'     => [
            'payment_id' => 'pay_mock_001',
            'amount'     => 25000,
            'currency'   => 'IDR',
            'status'     => 'succeeded',
        ],
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
            'path'   => '/api/webhooks/mock',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
