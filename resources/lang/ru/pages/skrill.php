<?php

return [

    'hint' => 'Подпись callback в стиле MD5/HMAC.',

    'summary' => <<<'TEXT'
Skrill отправляет статусный callback на ваш `status_url` и ожидает, что вы проверите сообщение с помощью `md5sig` — **MD5 В ВЕРХНЕМ РЕГИСТРЕ** от строго определённой конкатенации полей (например: `merchant_id + transaction_id + UPPERCASE(MD5(secret_word)) + mb_amount + mb_currency + status`). Доверять payload можно только если вычисленное вами значение совпадает с входящим `md5sig`. Skrill также поддерживает альтернативный `sha2sig` (SHA-2 в верхнем регистре) по запросу; он строится аналогично `md5sig`.

На практике держите проверку подписи на бэкенде (никогда не раскрывайте secret word) и хэшируйте **точные** значения параметров ровно такими, какими они были отправлены вам в callback. Сделайте endpoint идемпотентным (dedup по transaction или event ID), быстро возвращайте 2xx после сохранения и откладывайте некритичную работу. При отладке логируйте результат проверки и хэш тела, не записывая секреты. Будьте аккуратны с форматированием — суммы и валюты должны использоваться «как есть» при сборке строки подписи — чтобы сравнения были стабильны при ретраях и в разных окружениях.
TEXT
    ,

    // dari view Skrill
    'docs' => 'https://www.skrill.com/fileadmin/content/pdf/Skrill_Quick_Checkout_Guide_v10.3.pdf',

    'signature_notes' => [
        'Восстановите `md5sig` строго по инструкции: склейте документированные поля (например, merchant_id, transaction_id, UPPERCASE(MD5(secret_word)), mb_amount, mb_currency, status) и посчитайте **MD5 В ВЕРХНЕМ РЕГИСТРЕ**.',
        'Сравните с полученным `md5sig`; при включении у Skrill можно использовать `sha2sig` (SHA-2 в верхнем регистре) как альтернативу.',
        'Проверяйте только на сервере, используя ровно отправленные значения; делайте обработчик идемпотентным и быстро возвращайте 2xx.',
    ],

    'example_payload' => [
        'transaction_id' => 'SKR-001',
        'mb_amount' => '10.00',
        'mb_currency' => 'EUR',
        'status' => '2',
        'md5sig' => '<UPPERCASE_MD5>',
        'provider' => 'skrill',
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
            'path' => '/api/webhooks/skrill',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
