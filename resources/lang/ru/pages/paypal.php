<?php

return [

    'hint' => 'API Verify Webhook Signature.',

    'summary' => <<<'TEXT'
PayPal требует серверной проверки каждого webhook через официальное API Verify Webhook Signature. Ваш listener должен извлечь заголовки, приходящие с уведомлением—`PAYPAL-TRANSMISSION-ID`, `PAYPAL-TRANSMISSION-TIME`, `PAYPAL-CERT-URL` и `PAYPAL-TRANSMISSION-SIG`—а также ваш `webhook_id` и **сырой** body запроса (`webhook_event`). Отправьте эти данные в эндпоинт проверки и принимайте событие только если PayPal вернул успешный результат. Это заменяет более старые механизмы и упрощает единообразие для REST-продуктов.

Стройте consumer как быстрый, идемпотентный шлюз: сначала проверка, затем сохранение записи события, быстрый ответ 2xx и перенос тяжёлой работы в очередь. Для локальных проверок используйте constant-time сравнение, а при пересылке в PayPal сохраняйте raw bytes без изменений, чтобы избежать тонких багов из-за повторной сериализации. Применяйте узкое окно допуска вокруг `PAYPAL-TRANSMISSION-TIME`, чтобы сократить replay-окно, и логируйте минимум аудита (request ID, результат проверки, хэш body—без секретов). С таким подходом дубли и частичные сбои не приведут к двойной обработке, а аудит останется надежным при инцидентах.
TEXT
    ,

    // Dari view PayPal
    'docs' => 'https://developer.paypal.com/docs/api/webhooks/v1/',

    'signature_notes' => [
        'Соберите заголовки: PAYPAL-TRANSMISSION-ID, PAYPAL-TRANSMISSION-TIME, PAYPAL-CERT-URL, PAYPAL-TRANSMISSION-SIG; сохраните raw body.',
        'Вызовите Verify Webhook Signature API с этими значениями плюс webhook_id и webhook_event; принимайте только при успехе.',
        'Относитесь к проверке как к шлюзу; задайте короткое окно допуска для защиты от replay и сделайте обработку идемпотентной.',
        'Быстро верните 2xx, тяжёлую работу — в очередь; логируйте минимум диагностики (без секретов).',
    ],

    'example_payload' => [
        'id'          => 'WH-' . now()->timestamp,
        'event_type'  => 'PAYMENT.CAPTURE.COMPLETED',
        'resource'    => [
            'id'     => 'CAP-001',
            'status' => 'COMPLETED',
        ],
        'provider'    => 'paypal',
        'create_time' => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/paypal',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
