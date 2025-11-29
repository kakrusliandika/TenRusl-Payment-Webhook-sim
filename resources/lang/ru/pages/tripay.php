<?php

return [

    'hint' => 'X-Callback-Signature.',

    'summary' => <<<'TEXT'
TriPay отправляет callbacks на URL, который вы настроили, и добавляет заголовки, которые идентифицируют событие и помогают аутентифицировать отправителя. В частности, callbacks содержат `X-Callback-Event` со значением вроде `payment_status` и `X-Callback-Signature` для проверки подписи согласно документации TriPay. Ваш consumer должен прочитать эти заголовки, проверить подлинность запроса и только затем обновлять внутреннее состояние.

Проектируйте endpoint быстрым и идемпотентным. Используйте короткое окно актуальности, если присутствуют timestamps/nonces, и поддерживайте лёгкое хранилище дедупликации по reference или идентификаторам события. Быстро возвращайте 2xx после записи события, а побочные эффекты обрабатывайте асинхронно. Для прозрачности и реагирования на инциденты ведите аудит: время получения, метаданные события и результат проверки, без логирования секретов.
TEXT
    ,

    // dari view Tripay
    'docs' => 'https://tripay.co.id/developer',

    'signature_notes' => [
        'Проверьте `X-Callback-Event` (например, `payment_status`) и `X-Callback-Signature`.',
        'Валидируйте подпись согласно документации TriPay; отклоняйте при несоответствии или отсутствии заголовка.',
        'Держите обработку идемпотентной (дедуп по reference/event ID) и быстро подтверждайте (2xx).',
    ],

    'example_payload' => [
        'reference' => 'TRX-001',
        'status' => 'PAID',
        'amount' => 125000,
        'currency' => 'IDR',
        'provider' => 'tripay',
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
            'path' => '/api/webhooks/tripay',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
