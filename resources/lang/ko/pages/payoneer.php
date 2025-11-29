<?php

return [

    'hint' => '제품별 알림.',

    'summary' => <<<'TEXT'
Payoneer Checkout은 비동기 알림(웹훅)을 사용자가 제어하는 엔드포인트로 전송하여, 사용자의 브라우저 밖에서 안전하게 결제 상태를 정산(reconcile)할 수 있게 해줍니다. 플랫폼에서는 전용 notification URL을 정의하고, 스택에 맞는 전달 방식을 선택할 수 있습니다—POST(권장) 또는 GET, JSON 또는 폼 인코딩 파라미터. 다만 정확한 파라미터 세트와 서명/인증 패턴은 제품별로 다를 수 있으니, Payoneer 알림을 하나의 “통합 표면”으로 취급하세요: 이벤트를 식별하는 헤더/필드를 문서화하고, 가능한 경우 anti-replay 메타데이터를 포함하며, 상태를 변경하기 전에 반드시 진위를 검증해야 합니다.

운영 측면에서는, 좁고(idempotent) 결정적인 핸들러부터 분리하는 것이 좋습니다. 불변(immutable) 이벤트 레코드를 저장하고 2xx를 빠르게 반환하세요. 재시도 폭주(retry storm)를 피하기 위해 무거운 비즈니스 로직은 백그라운드 워커로 옮기세요. 중복 제거 키(dedup key)를 적용하고 timestamp/nonce가 있다면 짧은 freshness 윈도우를 강제하여 replay 또는 out-of-order 전달에 대비합니다. 추가 보장이 필요하면 notification URL에 제공자 발급 토큰(또는 자체 난수 시크릿)을 붙이고 서버에서 검증하세요. 마지막으로 팀을 위한 runbook을 배포해 엔드포인트, 포맷, 실패 코드, 그리고 사용하는 Payoneer 제품 변형에 맞춘 정확한 검증 절차를 문서화하고, 코드와 함께 버전 관리하세요.
TEXT
    ,

    // Dari view Payoneer
    'docs' => 'https://checkoutdocs.payoneer.com/docs/create-notification-endpoints',

    'signature_notes' => [
        '전용 알림 엔드포인트를 노출하세요(POST 권장); JSON 또는 폼 데이터를 수용합니다.',
        '제품 변형 문서에 따라 진위를 검증하세요(토큰 또는 서명 필드); 불일치 시 거부합니다.',
        '가능한 경우 timestamp/nonce freshness를 강제하고, 처리 로직을 idempotent하게 만드세요(dedup 키 저장).',
        '빠르게 ACK(2xx)하고 무거운 작업은 백그라운드 잡으로 오프로딩하세요; 시크릿을 로그하지 말고 감사 추적을 남기세요.',
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
