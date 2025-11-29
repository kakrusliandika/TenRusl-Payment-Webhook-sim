<?php

return [

    'hint' => '모든 요청에 포함되는 x-amzn-signature.',

    'summary' => <<<'TEXT'
Buy with Prime(BWP)는 모든 Webhook 에 서명을 추가하여, 요청이 실제로 Amazon 에서 왔는지 그리고 전송 중에 변조되지 않았는지 확인할 수 있도록 합니다. 각 요청에는 디지털 서명이 `x-amzn-signature` 헤더로 포함됩니다. 핸들러에서는 해당 이벤트 타입과 환경에 대해 BWP 문서에 정의된 대로 기대되는 서명을 정확히 재구성해야 합니다. 값이 일치하지 않으면 해당 호출을 거부해야 합니다. 요청과 함께 전달되는 timestamp/nonce 는 리플레이 공격 방어 전략의 일부로 간주하고, 엄격한 유효 시간 창을 적용하며, 중복을 막기 위해 이미 처리된 식별자를 저장해야 합니다.

운영 관점에서 엔드포인트는 빠르고 결정적(deterministic)이어야 합니다. 먼저 검증을 수행하고, 안전하게 기록된 뒤에 `2xx` 응답으로 확인(ack)한 다음, 가장 무거운 작업은 비동기적으로 처리합니다. allowlist 에 의존하더라도 IP 및 네트워크는 변경될 수 있다는 점을 기억해야 하며, 암호학적 검증이 핵심 신뢰 기반이라는 점을 잊지 마세요. 보안 감사 로그(요청 ID, 서명 존재 여부, 검증 결과, 본문에 대한 해시—secret 자체는 아님)를 유지하는 것이 좋습니다. 로컬 테스트에서는 환경 플래그 뒤에서 검증 단계를 stub 처리할 수 있지만, 프로덕션 경로에서는 항상 서명을 검증해야 합니다. 키를 교체하거나 canonicalization 규칙을 업데이트할 때는 신중히 롤포워드하고, 오류율을 모니터링하며, 어떤 헤더 세트와 해싱/카노니컬라이제이션을 구현했는지 정확히 문서화해서 스택 내 다른 서비스와 보조를 맞추세요.

통합 편의성 측면에서는, **명확한 실패 원인**(잘못된 서명, 만료된 timestamp, 잘못된 요청)을 노출하고 예측 가능한 재시도 동작을 위해 안정적인 오류 코드를 반환하는 것이 중요합니다. 여기에 애플리케이션 수준의 idempotency 와 리플레이 방어를 결합하면, 재시도나 트래픽 급증, 부분 장애 상황에서도 다운스트림 결제 상태 전이가 안전하게 유지됩니다.
TEXT
    ,

    // Dari view amazon_bwp.blade.php
    'docs' => 'https://documents.buywithprime.amazon.com/bwp-api/docs/subscribe-to-events',

    'signature_notes' => [
        '요청 헤더에서 `x-amzn-signature` 를 읽어옵니다.',
        'Buy with Prime 에 정의된 대로(공식 문서의 알고리즘/카노니컬라이제이션) 기대되는 서명을 재구성하고, 일치하지 않으면 요청을 거부합니다.',
        'timestamp/nonce 가 제공되는 경우, 리플레이 공격을 완화하기 위해 엄격한 freshness 윈도우를 적용하고, 처리된 ID 를 저장하여 중복을 방지합니다.',
    ],

    'example_payload' => [
        'eventType' => 'ORDER_COMPLETED',
        'data' => [
            'orderId' => 'BWP-001',
            'status' => 'COMPLETED',
            'amount' => 25000,
            'currency' => 'IDR',
        ],
        'provider' => 'amazon_bwp',
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
            'path' => '/api/webhooks/amazon_bwp',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
