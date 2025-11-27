<?php

return [

    'back_to_providers' => '프로바이더 목록으로 돌아가기',
    'view_details'      => '자세히 보기',
    'breadcrumb'        => '브레드크럼',

    /*
    |--------------------------------------------------------------------------
    | Providers Page / Search
    |--------------------------------------------------------------------------
    */
    'search_providers' => '프로바이더 검색...',
    'no_results'       => '검색과 일치하는 프로바이더가 없습니다.',

    /*
    |--------------------------------------------------------------------------
    | Payments (cards & partials)
    |--------------------------------------------------------------------------
    */
    'provider_default_name' => '프로바이더',
    'provider_label'        => '프로바이더',
    'provider_endpoints'    => '엔드포인트',
    'signature_notes_title' => '서명 메모',
    'example_payload_title' => '예제 페이로드',
    'view_docs'             => '문서 보기',

    'create_payment'  => '결제 생성 (idempotent)',
    'get_payment'     => '결제 상태 조회',
    'receive_webhook' => 'Webhook 수신',

    'endpoint_list' => [
        'create_payment' => [
            'method' => 'POST',
            'path'   => '/api/payments',
            'note'   => 'Idempotency-Key 필수',
        ],
        'get_payment' => [
            'method' => 'GET',
            'path'   => '/api/payments/{id}',
            'note'   => '결제 상태 조회',
        ],
        'receive_webhook' => [
            'method' => 'POST',
            'path'   => '/api/webhooks/{provider}',
            'note'   => 'mock/xendit/midtrans/…',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Errors / Empty states
    |--------------------------------------------------------------------------
    */
    'not_found'    => '찾고 있는 페이지를 찾을 수 없습니다.',
    'server_error' => '예기치 못한 서버 오류가 발생했습니다. 다시 시도해 주세요.',

    /*
    |--------------------------------------------------------------------------
    | Pluralization examples used on pages
    |--------------------------------------------------------------------------
    | Use via: trans_choice('pages.counts.results', $count, ['count' => $count])
    */
    'counts' => [
        'results' => '{0} 결과 없음|{1} :count개 결과|[2,*] :count개 결과',
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback summary untuk provider yang belum punya file sendiri
    |--------------------------------------------------------------------------
    */
    'default_summary' => '서명된 Webhook, idempotent 플로우 및 안전한 페이로드를 갖춘 시뮬레이션 결제 프로바이더로, 로컬 및 CI 테스트에 적합합니다.',
];
