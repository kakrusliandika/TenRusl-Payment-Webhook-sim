<?php

return [

    'hint' => 'Подпись публичным ключом (Classic) / секрет (Billing).',

    'summary' => <<<'TEXT'
Paddle Billing подписывает каждый webhook заголовком `Paddle-Signature`, который включает Unix-временную метку (`ts`) и подпись (`h1`). Для ручной проверки соберите подписываемую строку, склеив `ts`, двоеточие и точный raw request body (байт-в-байт, как был получен). Затем вычислите HMAC-SHA256 с секретом вашей notification destination и сравните результат с `h1` с помощью constant-time (timing-safe) сравнения. Paddle генерирует отдельный секрет для каждой notification destination — относитесь к нему как к паролю и не храните в репозитории.

Используйте официальные SDK или собственное middleware, чтобы выполнять проверку до любого парсинга. Поскольку тайминг и преобразования body — частые причины ошибок, убедитесь, что ваш фреймворк дает доступ к raw bytes (например, в Express: `express.raw({ type: 'application/json' })`) и применяйте короткое окно допуска для `ts`, чтобы предотвращать replay-атаки. После успешной проверки быстро подтверждайте (2xx), сохраняйте event ID для идемпотентности и переносите тяжелую работу в фоновые задачи. Это повышает надежность доставки и предотвращает дублирующие побочные эффекты при повторных отправках.

При миграции с Paddle Classic учтите: проверка сместилась с подписи публичным ключом на HMAC, основанный на секрете, для Billing. Обновите runbook и управление секретами, а при раскатке изменений мониторьте метрики верификации. Понятные логи (без секретов) и детерминированные ответы на ошибки существенно упрощают разбор инцидентов и поддержку партнеров.
TEXT
    ,

    // Dari view Paddle
    'docs' => 'https://developer.paddle.com/webhooks/signature-verification',

    'signature_notes' => [
        'Считайте заголовок `Paddle-Signature`; извлеките значения `ts` и `h1`.',
        'Соберите signed payload = `ts + ":" + <raw request body>`; вычислите HMAC с секретом вашего endpoint.',
        'Сравните ваш хэш с `h1` через timing-safe функцию; применяйте короткое окно допуска для `ts`, чтобы предотвратить replay.',
        'Предпочитайте официальные SDK или middleware верификации; JSON парсить только после успешной проверки.',
    ],

    'example_payload' => [
        'event_id' => 'evt_'.now()->timestamp,
        'event_type' => 'transaction.completed',
        'provider' => 'paddle',
        'data' => [
            'transaction_id' => 'txn_001',
            'amount' => 25000,
            'currency_code' => 'IDR',
            'status' => 'completed',
        ],
        'occurred_at' => now()->toIso8601String(),
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
            'path' => '/api/webhooks/paddle',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
