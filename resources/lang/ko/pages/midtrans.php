<?php

return [

    'hint' => 'signature_key 검증.',

    'summary' => <<<'TEXT'
Midtrans는 각 HTTP(S) 알림(notification) 본문에 계산된 `signature_key`를 포함하여, 처리 전에 발신자가 Midtrans인지 검증할 수 있게 합니다. 공식 공식은 명확하고 안정적입니다:
    SHA512(order_id + status_code + gross_amount + ServerKey)
알림 body에서 `order_id`, `status_code`, `gross_amount` 값을 **문자열 그대로** 가져오고, 여기에 비공개 `ServerKey`를 이어 붙여 입력 문자열을 만든 뒤 SHA-512 hex digest를 계산합니다. 계산 결과를 `signature_key`와 constant-time(타이밍 안전) 비교로 대조하세요. 검증이 실패하면 알림은 폐기해야 합니다. 검증이 성공한 경우에는 문서화된 필드(예: `transaction_status`)를 사용해 상태 머신을 진행하되, 빠르게 2xx로 ACK하고, 무거운 작업은 큐/백그라운드로 넘기며, 재시도나 순서 뒤바뀜에 대비해 업데이트는 idempotent하게 처리하세요.

자주 발생하는 함정은 두 가지입니다: 포맷과 타입 변환. 문자열을 구성할 때 `gross_amount`는 제공된 값을 그대로 유지해야 합니다(로컬라이즈 금지, 소수점 변경 금지). 또한 trim이나 개행/공백 변경을 피하세요. 레이스 컨디션을 막기 위해 이벤트/주문 단위 deduplication 키를 저장하고, secret을 노출하지 않도록 검증 결과와 body 해시만 로그로 남기세요. 마지막으로 엔드포인트에는 rate limiting과 명확한 실패 코드를 적용하여, 모니터링이 일시적 오류(재시도 가능)와 영구 거절(서명 불일치)을 구분할 수 있게 하세요.
TEXT
    ,

    // dari view midtrans
    'docs' => 'https://docs.midtrans.com/docs/https-notification-webhooks',

    'signature_notes' => [
        'Body에서 `order_id`, `status_code`, `gross_amount`를 (문자열로) 가져와 `ServerKey`를 뒤에 붙입니다.',
        '`SHA512(order_id + status_code + gross_amount + ServerKey)`를 계산하고 `signature_key`와 constant-time로 비교합니다.',
        '불일치면 거부(폐기)하고, 일치하면 `transaction_status`로 상태를 업데이트합니다. 처리는 idempotent하게 유지하고 2xx를 신속히 반환하세요.',
        '연결 시 `gross_amount` 포맷 변경과 불필요한 공백/개행이 섞이지 않도록 주의하세요.',
    ],

    'example_payload' => [
        'order_id' => 'ORDER-001',
        'status_code' => '200',
        'gross_amount' => '25000.00',
        'transaction_status' => 'settlement',
        'signature_key' => '<sha512>',
        'provider' => 'midtrans',
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
            'path' => '/api/webhooks/midtrans',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
