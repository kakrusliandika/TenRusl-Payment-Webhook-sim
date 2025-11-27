<?php

return [

    'hint' => 'MD5/HMAC 스타일 콜백 서명.',

    'summary' => <<<'TEXT'
Skrill은 `status_url`로 상태 콜백을 POST하며, `md5sig`를 사용해 메시지를 검증하길 요구합니다. `md5sig`는 명확히 정의된 필드 결합(예: `merchant_id + transaction_id + UPPERCASE(MD5(secret_word)) + mb_amount + mb_currency + status`)으로 만든 문자열을 **대문자(UPPERCASE) MD5**로 해시한 값입니다. 계산한 값이 수신한 `md5sig`와 일치할 때만 payload를 신뢰해야 합니다. Skrill은 요청 시 `sha2sig`(대문자 SHA-2)라는 대체 옵션도 제공하며, 이는 `md5sig`와 유사한 방식으로 구성됩니다.

실무에서는 서명 검증을 반드시 백엔드에서 수행하세요(Secret word를 절대 노출하지 않기). 콜백으로 전달된 **그대로의** 파라미터 값을 사용해 해시를 계산해야 합니다. 엔드포인트는 idempotent하게 만들고(트랜잭션 또는 이벤트 ID로 중복 제거), 저장 후 빠르게 2xx를 반환하며, 비핵심 작업은 지연 처리하세요. 디버깅 시에는 검증 결과와 바디 해시를 기록하되, 로그에 비밀값을 남기지 마세요. 또한 포맷에 주의하세요—금액과 통화 필드는 서명 문자열 구성 시 반드시 원문 그대로 사용해야—재시도와 환경 차이에서도 비교가 안정적입니다.
TEXT
    ,

    // dari view Skrill
    'docs' => 'https://www.skrill.com/fileadmin/content/pdf/Skrill_Quick_Checkout_Guide_v10.3.pdf',

    'signature_notes' => [
        '`md5sig`를 정확히 재구성: 문서화된 필드(예: merchant_id, transaction_id, UPPERCASE(MD5(secret_word)), mb_amount, mb_currency, status)를 순서대로 연결하고 **UPPERCASE MD5**를 계산.',
        '수신한 `md5sig`와 비교; Skrill에서 활성화된 경우 `sha2sig`(UPPERCASE SHA-2)도 옵션으로 사용 가능.',
        '검증은 서버에서만 수행하고, 전송된 값을 그대로 사용; 핸들러를 idempotent하게 유지하고 2xx를 빠르게 반환.',
    ],

    'example_payload' => [
        'transaction_id' => 'SKR-001',
        'mb_amount'      => '10.00',
        'mb_currency'    => 'EUR',
        'status'         => '2',
        'md5sig'         => '<UPPERCASE_MD5>',
        'provider'       => 'skrill',
        'sent_at'        => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/skrill',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
