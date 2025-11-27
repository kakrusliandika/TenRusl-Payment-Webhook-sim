<?php

return [

    'hint' => 'Client-Id/Request-* 헤더 기반 서명.',

    'summary' => <<<'TEXT'
DOKU는 헤더 기반의 카노니컬(canonical) 서명으로 HTTP Notification을 보호하며, 어떤 payload든 처리하기 전에 반드시 이 서명을 검증해야 합니다. 각 콜백에는 `Signature` 헤더가 포함되며 값은 `HMACSHA256=<base64>` 형태입니다. 기대값을 재구성하려면 먼저 요청 바디에 대한 `Digest`를 계산합니다. 수신한 raw JSON 바이트에 대해 SHA-256을 계산한 뒤 base64로 인코딩합니다. 다음으로, 아래 5개 구성요소를 정확한 순서/표기로 줄바꿈(newline)으로 구분한 문자열을 만듭니다:
  • `Client-Id:<value>`
  • `Request-Id:<value>`
  • `Request-Timestamp:<value>`
  • `Request-Target:<path-of-your-notification-url>` (예: `/payments/notifications`)
  • `Digest:<base64-of-SHA256(body)>`
그런 다음 해당 카노니컬 문자열에 대해 DOKU Secret Key를 키로 사용하여 SHA-256 HMAC을 계산하고, 결과를 base64로 인코딩한 뒤 `HMACSHA256=`를 앞에 붙입니다. 마지막으로 `Signature` 헤더와 constant-time 비교로 대조합니다. 불일치, 구성요소 누락, 값 형식 오류는 모두 인증 실패로 처리해야 하며 요청을 즉시 거부해야 합니다.

복원력과 안전을 위해 유효한 알림은 빠르게(2xx) ACK하고, 무거운 작업은 백그라운드 잡으로 넘겨 재시도를 유발하지 않도록 하세요. 처리된 식별자(예: `Request-Id` 또는 바디의 이벤트 ID)를 기록해 컨슈머를 idempotent하게 만드세요. freshness도 검증해야 합니다. `Request-Timestamp`는 리플레이 공격을 막기 위해 엄격한 시간 창 내에 있어야 하며, `Request-Target`이 실제 라우트 경로와 일치하는지도 확인해 canonicalization 버그를 피합니다. 파싱은 DOKU 권장처럼 과도하게 엄격하지 않게: 알 수 없는 필드는 무시하고, 깨지기 쉬운 파서보다 스키마 진화를 우선하세요. 장애 대응 시에는 필수 헤더 존재 여부, 계산된 digest/signature(절대 secret은 로그에 남기지 않기), 그리고 바디 해시를 로그로 남겨 민감 정보 유출 없이 감사에 도움이 되게 하세요.
TEXT
    ,

    // dari view DOKU
    'docs' => 'https://developers.doku.com/get-started-with-doku-api/signature-component/non-snap/signature-component-from-request-header',

    'signature_notes' => [
        '헤더 `Client-Id`, `Request-Id`, `Request-Timestamp`, `Signature`를 읽고, `Request-Target`(본인 라우트 path)을 추정합니다.',
        '`Digest = base64( SHA256(raw JSON body) )`를 계산합니다.',
        'Client-Id, Request-Id, Request-Timestamp, Request-Target, Digest 순서로 각 항목을 한 줄씩 넣어 카노니컬 문자열을 만듭니다(끝에 trailing newline 없음).',
        'expected = `HMACSHA256=base64( HMAC-SHA256(canonical, SecretKey) )`를 계산하고, `Signature`와 constant-time 비교합니다.',
        'timestamp freshness를 강제하고, 처리를 idempotent하게 만들며, 빠르게 ACK(2xx)하고 무거운 작업은 오프로드합니다.',
    ],

    'example_payload' => [
        'order'       => ['invoice_number' => 'INV-001'],
        'transaction' => ['status' => 'SUCCESS'],
        'provider'    => 'doku',
        'sent_at'     => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/doku',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
