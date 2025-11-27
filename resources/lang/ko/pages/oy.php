<?php

return [

    'hint' => '프로바이더별 콜백 서명.',

    'summary' => <<<'TEXT'
OY! 콜백은 파트너 요청에 대해 등록된 API 키와 소스 IP allowlisting을 기반으로 한 더 넓은 보안 체계의 일부입니다. 또한 OY!는 Authorization Callback 기능을 제공하여, 콜백이 시스템에 도달하기 전에 이를 제어하고 승인할 수 있게 해 주며, 의도치 않은 상태 변경을 막기 위한 명시적인 게이트를 추가합니다. 실무에서는 검증되기 전까지 모든 인바운드 콜백을 신뢰하지 말고, freshness(타임스탬프/nonce 윈도우)를 강제하며, 재시도와 순서 뒤바뀜(out-of-order) 배송에서도 안전하도록 컨슈머를 idempotent하게 설계해야 합니다.

퍼블릭 프로바이더마다 콜백 서명 방식이 다르기 때문에, 이 시뮬레이터는 공유 시크릿으로 “정확한 raw request body” 위에 계산하는 HMAC 헤더(예: `X-Callback-Signature`)를 사용한 강화된 베이스라인을 보여 줍니다. 이는 프로덕션에서 쓰는 동일한 원칙을 설명합니다: raw 바이트 해싱(재직렬화 금지), constant-time 비교, 짧은 리플레이 윈도우. 작은 dedup 저장소와 빠른 2xx 응답을 함께 사용하면, 프로바이더의 재시도 로직을 건강하게 유지하면서 중복 부작용을 피할 수 있습니다.

운영 측면에서는 감사 추적(audit trail: 수신 시각, 검증 결과, 바디 해시—시크릿은 제외)을 유지하고, 시크릿을 안전하게 로테이션하며, 검증 실패율을 모니터링하세요. allowlist에 의존하더라도 변경될 수 있음을 기억해야 하며, 암호학적 검증(또는 OY의 명시적 승인 게이트)이 주요 신뢰 기준으로 남아야 합니다. 엔드포인트는 좁고 예측 가능하며 문서화가 잘 되어 있어야 다른 서비스와 팀원들이 자신 있게 재사용할 수 있습니다.
TEXT
    ,

    // dari view OY
    'docs' => 'https://docs.oyindonesia.com/',

    'signature_notes' => [
        'OY! 보안 체계 사용: 등록된 API 키 + 파트너 요청을 위한 소스 IP allowlisting.',
        'Authorization Callback(대시보드)을 활용해 콜백이 시스템에 도달하기 전에 승인하세요.',
        '이 시뮬레이터에서는 모범 사례 모델로 `X-Callback-Signature = HMAC-SHA256(raw_body, secret)`를 검증하며, constant-time 비교와 freshness 체크를 적용합니다.',
        '처리를 idempotent하게 만들고, 프로바이더 재시도가 정상 동작하도록 2xx를 신속히 반환하세요.',
    ],

    'example_payload' => [
        'id'       => 'evt_' . now()->timestamp,
        'type'     => 'payment.completed',
        'provider' => 'oy',
        'data'     => [
            'partner_trx_id' => 'PRT-001',
            'amount'         => 25000,
            'currency'       => 'IDR',
            'status'         => 'COMPLETED',
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
            'path'   => '/api/webhooks/oy',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
