<?php

return [

    'hint' => 'Подпись с заголовками Client-Id/Request-*.',

    'summary' => <<<'TEXT'
DOKU защищает HTTP-уведомления канонической подписью, сформированной на основе заголовков, и вы должны проверять её до того, как обрабатывать любой payload. Каждый callback приходит с заголовком `Signature`, значение которого имеет вид `HMACSHA256=<base64>`. Чтобы получить ожидаемое значение, сначала вычислите `Digest` для тела запроса: SHA-256 по сырым байтам JSON, затем закодируйте результат в base64. Далее соберите строку, разделённую переводами строк, из пяти компонентов в **точно таком** порядке и написании:
  • `Client-Id:<value>`
  • `Request-Id:<value>`
  • `Request-Timestamp:<value>`
  • `Request-Target:<path-of-your-notification-url>` (например, `/payments/notifications`)
  • `Digest:<base64-of-SHA256(body)>`
Затем вычислите HMAC-SHA256 по этой канонической строке, используя DOKU Secret Key в качестве ключа, закодируйте результат в base64 и добавьте префикс `HMACSHA256=`. В конце сравните с заголовком `Signature` через сравнение в постоянное время (constant-time). Любое несовпадение, отсутствие компонента или некорректный формат — это ошибка аутентификации, и запрос нужно немедленно отклонить.

Для надёжности и безопасности быстро подтверждайте валидные уведомления (2xx) и переносите тяжёлую работу в фоновые задачи, чтобы не провоцировать повторы. Сделайте потребитель идемпотентным: фиксируйте обработанные идентификаторы (например, `Request-Id` или event ID в теле). Проверяйте актуальность: `Request-Timestamp` должен попадать в узкое окно времени для защиты от replay; также убедитесь, что `Request-Target` совпадает с вашим реальным маршрутом, чтобы избежать ошибок каноникализации. При разборе следуйте рекомендациям DOKU быть нестрогими: игнорируйте неизвестные поля и предпочитайте эволюцию схемы хрупким парсерам. Во время инцидентов логируйте наличие обязательных заголовков, вычисленные digest/signature (никогда не логируйте secret) и хэш тела, чтобы помочь аудиту без утечки чувствительных данных.
TEXT
    ,

    // dari view DOKU
    'docs' => 'https://developers.doku.com/get-started-with-doku-api/signature-component/non-snap/signature-component-from-request-header',

    'signature_notes' => [
        'Считайте заголовки: `Client-Id`, `Request-Id`, `Request-Timestamp`, `Signature`, и определите `Request-Target` (путь вашего роута).',
        'Вычислите `Digest = base64( SHA256(raw JSON body) )`.',
        'Соберите каноническую строку по строкам: Client-Id, Request-Id, Request-Timestamp, Request-Target, Digest (в таком порядке, каждый на своей строке, без завершающего перевода строки).',
        'Вычислите expected = `HMACSHA256=base64( HMAC-SHA256(canonical, SecretKey) )`; сравните с `Signature` использованием constant-time.',
        'Проверяйте актуальность timestamp; обеспечьте идемпотентность; быстро ACK (2xx) и выносите тяжёлую работу в фон.',
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
