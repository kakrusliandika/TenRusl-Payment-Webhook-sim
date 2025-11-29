<?php

return [

    'hint' => 'x-timestamp + body 에 대한 HMAC-SHA256.',

    'summary' => <<<'TEXT'
Airwallex Webhook에는 서명이 포함되어 있어, 데이터베이스를 건드리기 전에 요청의 진위(Authenticity)와 무결성(Integrity)을 검증할 수 있습니다. 각 요청에는 `x-timestamp` 와 `x-signature` 라는 두 가지 핵심 헤더가 포함됩니다. 메시지를 검증하려면 먼저 수신한 HTTP 바디를 원본 그대로 읽어들이고, 그 앞에 `x-timestamp` 값(문자열)을 이어 붙여 다이제스트 입력을 만듭니다. 그런 다음 이 값을 대상으로, 알림 URL에 설정된 공유 시크릿을 키로 사용하는 SHA-256 기반 HMAC 을 계산합니다. Airwallex 는 결과를 **16진수 다이제스트(hex digest)** 로 기대하므로, 이 값을 `x-signature` 헤더와 상수 시간 비교(constant-time comparison)를 사용해 비교해야 타이밍 정보가 새어 나가지 않습니다. 서명이 일치하지 않거나 타임스탬프가 없거나 유효하지 않다면, 반드시 안전하게 실패(fail closed) 처리하고 2xx 가 아닌 응답을 반환해야 합니다.

어떤 Webhook 시스템에서도 재전송 공격(replay)은 현실적인 위험이므로, `x-timestamp` 에 대해 유효 시간(freshness window)을 적용하는 것이 좋습니다. 너무 오래되었거나 너무 미래의 타임스탬프를 가진 메시지는 거부하고, 이미 처리한 이벤트 ID 를 저장하여 이후 단계에서 발생할 수 있는 부작용을 중복 제거합니다(애플리케이션 레이어에서의 idempotency). 검증이 끝날 때까지 payload 는 신뢰할 수 없는 데이터로 취급해야 합니다. JSON 을 다시 직렬화한 뒤 해시하지 말고, 수신 시점의 원본 바이트(raw bytes) 그대로를 사용하여 공백/순서 차이에서 오는 미묘한 불일치를 피해야 합니다. 검증이 성공하면 즉시 `2xx` 응답을 돌려주고, 무거운 작업은 비동기적으로 처리해 재시도 로직을 건강하게 유지하고 우발적인 중복을 줄이세요.

로컬 및 CI 플로우를 위해 Airwallex 는 우수한 도구를 제공합니다. 대시보드에서 알림 URL 을 설정하고, 예제 payload 를 미리 보고, 엔드포인트로 **테스트 이벤트를 전송**할 수 있습니다. 디버깅 시에는 수신한 `x-timestamp`, 계산한 서명의 프리뷰(시크릿은 절대 로그에 남기지 말 것), 그리고 존재한다면 이벤트 ID 를 로그로 남기세요. 시크릿 키를 로테이션할 때는 안전하게 점진적으로 교체하고, 서명 오류율을 모니터링해야 합니다. 마지막으로, 검증·중복 제거·재시도·에러 응답까지 전체 체인을 문서화해 두면, 팀원들도 동일한 raw-body 해시 규칙과 시간 창을 사용해 결과를 재현할 수 있습니다.
TEXT
    ,

    'docs' => 'https://www.airwallex.com/docs/developer-tools/webhooks/listen-for-webhook-events',

    'signature_notes' => [
        '헤더에서 `x-timestamp` 와 `x-signature` 를 추출합니다.',
        'value_to_digest = <x-timestamp> + <원본 HTTP 바디> (바이트 그대로) 를 구성합니다.',
        'expected = HMAC-SHA256(value_to_digest, <webhook secret>) 을 HEX 문자열로 계산한 뒤, `x-signature` 와 상수 시간 비교로 검사합니다.',
        '서명이 일치하지 않거나 타임스탬프가 오래된 경우 요청을 거부하고, 처리된 이벤트 ID 를 중복 제거해 idempotency 를 보장합니다.',
    ],

    'example_payload' => [
        'id' => 'evt_'.now()->timestamp,
        'type' => 'payment_intent.succeeded',
        'data' => [
            'payment_intent_id' => 'pi_awx_001',
            'amount' => 25000,
            'currency' => 'IDR',
            'status' => 'succeeded',
        ],
        'provider' => 'airwallex',
        'created_at' => now()->toIso8601String(),
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
            'path' => '/api/webhooks/airwallex',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
