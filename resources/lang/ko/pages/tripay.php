<?php

return [

    'hint' => 'X-Callback-Signature.',

    'summary' => <<<'TEXT'
TriPay는 설정한 URL로 콜백을 전송하며, 이벤트를 식별하고 발신자를 인증하는 데 도움이 되는 헤더를 포함합니다. 특히 콜백에는 `X-Callback-Event`(예: `payment_status`)와 TriPay 문서에 명시된 방식으로 서명을 검증하기 위한 `X-Callback-Signature`가 포함됩니다. 컨슈머(consumer)는 이 헤더들을 읽고 요청의 진위를 검증한 뒤에만 내부 상태를 업데이트해야 합니다.

엔드포인트는 빠르고 idempotent하게 설계하세요. timestamp/nonce가 제공된다면 짧은 freshness window를 적용하고, reference 또는 이벤트 식별자를 키로 하는 가벼운 dedup 저장소를 유지하세요. 이벤트가 기록되면 즉시 2xx로 응답하고, 부수 효과는 비동기로 처리합니다. 투명성과 인시던트 대응을 위해 수신 시각, 이벤트 메타데이터, 검증 결과를(비밀값은 제외) 기록하는 감사 추적(audit trail)을 유지하세요.
TEXT
    ,

    // dari view Tripay
    'docs' => 'https://tripay.co.id/developer',

    'signature_notes' => [
        '`X-Callback-Event`(예: `payment_status`)와 `X-Callback-Signature`를 확인합니다.',
        'TriPay 문서대로 서명을 검증하고, 불일치하거나 헤더가 없으면 거부합니다.',
        '처리를 idempotent하게 유지( reference/event ID로 중복 제거 )하고 빠르게(2xx) 승인합니다.',
    ],

    'example_payload' => [
        'reference' => 'TRX-001',
        'status'    => 'PAID',
        'amount'    => 125000,
        'currency'  => 'IDR',
        'provider'  => 'tripay',
        'sent_at'   => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/tripay',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
