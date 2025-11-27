<?php

return [

    'hint' => 'RSA (DANA 공개 키로 검증).',

    'summary' => <<<'TEXT'
DANA는 **비대칭** 서명 방식을 사용합니다. 요청은 개인키로 서명되고, 통합사는 공식 **DANA 공개 키**로 이를 검증합니다. 실제로는 webhook 헤더(예: `X-SIGNATURE`)에서 서명을 가져와 base64 디코딩한 뒤, RSA-2048 + SHA-256을 사용해 수신한 raw HTTP 요청 본문을 해당 서명과 대조하여 검증합니다. 검증 결과가 “성공(positive)”일 때만 payload를 신뢰할 수 있는 것으로 취급해야 합니다. 검증에 실패하거나—또는 서명/헤더가 없으면—non-2xx로 응답하고 처리를 중단하세요.

Webhook은 재시도되거나 순서가 뒤섞여 도착할 수 있으므로, 핸들러는 idempotent하게 설계해야 합니다. 고유한 이벤트 식별자를 저장하고 중복은 즉시 차단하며, timestamp/nonce가 있다면 freshness를 검증해 리플레이를 완화하고, 서명 검증이 성공하기 전까지는 모든 필드를 신뢰하지 마세요. 검증 전에 JSON을 다시 직렬화하지 말고, 네트워크로 들어온 그대로의 바이트를 대상으로 검증/해시하세요. 시크릿과 개인키는 로그에 남기지 말고, 필요하면 고수준 진단 정보(검증 결과, 본문 해시, 이벤트 ID)만 기록하며 저장된 로그도 안전하게 보호하세요.

팀 차원에서는 짧은 런북을 공개하세요. DANA 공개 키를 로드/로테이션하는 방법, 사용하는 각 언어/런타임에서의 검증 방법, 통합에서의 정확한 string-to-sign 규칙, 영구 실패 vs 일시 실패 기준을 포함합니다. 여기에 견고한 retry/backoff 정책, 제한된 작업 큐, 헬스 체크, 검증 실패 알림을 결합하면, 부하 상황에서도 안전하고 재시도에 강하며 DANA가 요구하는 암호학적 검증을 충족하는 webhook 소비자를 만들 수 있습니다.
TEXT
    ,

    // dari view DANA
    'docs' => 'https://dashboard.dana.id/api-docs-v2/guide/authentication',

    'signature_notes' => [
        '`X-SIGNATURE` 헤더 값에 대해 base64 디코딩을 수행합니다.',
        '공식 DANA 공개 키를 사용해, 수신한 raw HTTP body 그대로에 대해 RSA-2048 + SHA-256 검증을 수행합니다. 검증이 성공(positive)일 때만 수락합니다.',
        '서명이 누락/무효이거나 payload가 잘못된 webhook은 거부합니다. 검증 성공 전에는 어떤 데이터도 신뢰하지 마세요.',
    ],

    'example_payload' => [
        'id'       => 'evt_' . now()->timestamp,
        'type'     => 'payment.paid',
        'provider' => 'dana',
        'data'     => [
            'transaction_id' => 'DANA-001',
            'amount'         => 25000,
            'currency'       => 'IDR',
            'status'         => 'SUCCESS',
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
            'path'   => '/api/webhooks/dana',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
