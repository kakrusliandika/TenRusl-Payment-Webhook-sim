<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Site / Brand
    |--------------------------------------------------------------------------
    */
    'site_name' => 'TenRusl',

    /*
    |--------------------------------------------------------------------------
    | Global Navigation
    |--------------------------------------------------------------------------
    */
    'home_title'      => 'Inicio',
    'providers_title' => 'Proveedores',
    'features'        => 'Funciones',
    'endpoints'       => 'Endpoints',
    'signature'       => 'Firma',
    'tooling'         => 'Herramientas',
    'openapi'         => 'OpenAPI',
    'github'          => 'GitHub',

    /*
    |--------------------------------------------------------------------------
    | Accessibility / ARIA
    |--------------------------------------------------------------------------
    */
    'aria' => [
        'primary_nav'  => 'Navegación principal',
        'utility_nav'  => 'Navegación de utilidades',
        'toggle_menu'  => 'Alternar el menú de navegación principal',
        'toggle_theme' => 'Alternar tema claro y oscuro',
        'skip_to_main' => 'Saltar directamente al contenido principal',
        'language'     => 'Cambiar el idioma de la interfaz',
    ],

    /*
    |--------------------------------------------------------------------------
    | Footer / Legal
    |--------------------------------------------------------------------------
    */
    'terms'       => 'Términos',
    'privacy'     => 'Privacidad',
    'cookies'     => 'Cookies',
    'footer_demo' => 'Entorno de demostración de arquitectura de pagos para aprender, probar y explicar flujos modernos centrados en webhooks.',
    'build'       => 'Build',

    /*
    |--------------------------------------------------------------------------
    | SEO Defaults (override per page via layout/seo.blade.php)
    |--------------------------------------------------------------------------
    */
    'meta' => [
        'default_title'       => 'Simulador de Webhooks de Pago',
        'default_description' => 'Simula flujos de pago reales con operaciones idempotentes, firmas de webhooks verificadas, deduplicación de eventos y un comportamiento de reintentos y backoff realista, todo sin exponer ni depender de credenciales reales de pasarelas de pago.',
        'default_image_alt'   => 'Diagrama de la arquitectura del Simulador de Webhooks de Pago que muestra proveedores de pago, callbacks de webhooks, reintentos y actualizaciones de estado.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Home (Landing)
    |--------------------------------------------------------------------------
    */
    'home' => [
        'title'       => 'Simulador de Webhooks de Pago',
        'description' => 'Estabiliza tu integración de pagos en un entorno sandbox seguro similar a producción. Practica el manejo de Idempotency-Key, la verificación de firmas, la deduplicación de eventos y los reintentos con backoff antes de apuntar a una pasarela de pago real.',
        'hero' => [
            'heading_prefix' => 'PWS',
            'heading_emph'   => 'Idempotente',
            'lede'           => 'Simula eventos de pago entrantes desde múltiples proveedores, verifica firmas sobre el cuerpo bruto de la solicitud, deduplica reintentos ruidosos y observa estrategias de backoff realistas de extremo a extremo sin tocar credenciales o webhooks de producción.',
            'cta_docs'       => 'Referencia OpenAPI',
            'cta_features'   => 'Explorar funciones',
            'cta_github'     => 'Ver código en GitHub',
            'stats' => [
                'providers' => ['label' => 'Proveedores simulados'],
                'tests'     => ['label' => 'Pruebas automatizadas'],
                'openapi'   => ['label' => 'Endpoints documentados'],
            ],
            'chip'           => 'POST : /api/webhooks/mock con una carga útil JSON firmada',
            'simulate'       => 'Simula webhooks de pago reales con idempotencia, comprobaciones de firma y lógica de reintentos antes de llegar a producción.',
        ],
        'sections' => [
            'features' => [
                'title' => 'Funciones',
                'lede'  => 'Refuerza y estabiliza tu integración de pagos con simulaciones realistas y repetibles que reflejan cómo los gateways modernos envían, firman y reintentan eventos de webhooks en producción.',
                'ship' => 'Lanza flujos de pago más seguros sin usar credenciales reales de gateways.',
                'items' => [
                    [
                        'title' => 'Idempotencia',
                        'desc'  => 'Practica el manejo correcto de Idempotency-Key para que las solicitudes duplicadas, los reenvíos y los reintentos del cliente se conviertan en un único registro de pago coherente en lugar de corromper el estado.',
                    ],
                    [
                        'title' => 'Verificación de firma',
                        'desc'  => 'Valida el cuerpo bruto de la solicitud y las cabeceras del proveedor mediante HMAC, marcas de tiempo y secretos compartidos, obteniendo un entorno seguro para perfeccionar tu lógica de verificación de firmas antes de ir a producción.',
                    ],
                    [
                        'title' => 'Deduplicación y reintentos',
                        'desc'  => 'Simula entregas duplicadas de webhooks y backoff exponencial para que puedas diseñar controladores idempotentes y resistentes que puedan ejecutarse varias veces sin efectos secundarios.',
                    ],
                    [
                        'title' => 'OpenAPI',
                        'desc'  => 'Explora documentación OpenAPI interactiva que describe cada endpoint de pago y webhook, con esquemas, ejemplos y fragmentos de curl listos para usar.',
                    ],
                    [
                        'title' => 'Postman',
                        'desc'  => 'Importa la colección de Postman curada para llamar endpoints, ajustar cargas útiles y ensayar condiciones de error desde tu cliente de API favorito en unos pocos clics.',
                    ],
                    [
                        'title' => 'Integración con CI',
                        'desc'  => 'Conecta el simulador a tus pipelines de CI para que las pruebas, linters y verificaciones de contrato se ejecuten en cada push y detecten regresiones de integración mucho antes de llegar a producción.',
                    ],
                ],
            ],
            'endpoints' => [
                'title' => 'Endpoints',
                'lede'  => 'Define una superficie pequeña pero realista: crea pagos, consulta estados y recibe webhooks al estilo de los proveedores con semántica idempotente.',
                'cards' => [
                    [
                        'title' => 'POST /api/payments',
                        'desc'  => 'Crea un nuevo registro de pago simulado. Este endpoint requiere una cabecera Idempotency-Key para que puedas verificar cómo tu aplicación deduplica intentos de pago repetidos.',
                    ],
                    [
                        'title' => 'GET /api/payments/{id}',
                        'desc'  => 'Obtén el estado más reciente de un pago específico, incluidas las transiciones de estado y cualquier evento de webhook asociado que el simulador haya procesado.',
                    ],
                    [
                        'title' => 'POST /api/webhooks/{provider}',
                        'desc'  => 'Recibe callbacks de webhooks para un proveedor concreto (mock, xendit, midtrans y más) usando cargas útiles, cabeceras y esquemas de firma realistas adaptados a cada integración.',
                    ],
                ],
            ],
            'providers' => [
                'title' => 'Proveedores',
                'lede'  => 'Prueba flujos de webhooks realistas de varios gateways de pago en un solo sandbox sin usar credenciales reales.',
                'cta_all' => 'Ver todos los proveedores',
                'map'   => [
                    'mock'         => 'Mock',
                    'xendit'       => 'Xendit',
                    'midtrans'     => 'Midtrans',
                    'stripe'       => 'Stripe',
                    'paypal'       => 'PayPal',
                    'paddle'       => 'Paddle',
                    'lemonsqueezy' => 'Lemon Squeezy',
                    'airwallex'    => 'Airwallex',
                    'tripay'       => 'Tripay',
                    'doku'         => 'DOKU',
                    'dana'         => 'DANA',
                    'oy'           => 'OY!',
                    'payoneer'     => 'Payoneer',
                    'skrill'       => 'Skrill',
                    'amazon_bwp'   => 'Amazon BWP',
                ],
            ],
            'signature' => [
                'title' => 'Verificación de firma',
                'lede'  => 'Verifica cargas útiles de webhooks en bruto con firmas HMAC, marcas de tiempo y comprobaciones estrictas de cabeceras.',
                'compare'  => 'Compara la firma que envía el proveedor con la que calculas a partir del cuerpo bruto de la solicitud, el secreto compartido y la marca de tiempo, usando una comparación en tiempo constante.',
                'reject'    => 'Rechaza automáticamente firmas que no coinciden y marcas de tiempo caducadas.',
                'cards' => [
                    [
                        'title' => 'HMAC / Marca de tiempo',
                        'desc'  => 'Experimenta con firmas HMAC con marca de tiempo que protegen contra ataques de repetición. Observa cómo los hashes del cuerpo bruto, los secretos compartidos y las cabeceras firmadas se combinan para formar una traza de auditoría verificable para cada evento de webhook.',
                    ],
                    [
                        'title' => 'Basado en cabeceras',
                        'desc'  => 'Trabaja con cabeceras y tokens específicos de cada proveedor, desde secretos bearer simples hasta envoltorios de firma estructurados, para garantizar que tu aplicación rechaza cargas útiles falsas sin bloquear eventos legítimos.',
                    ],
                ],
            ],
            'tooling' => [
                'title' => 'Herramientas',
                'work'  => 'Funciona con tu entorno de desarrollo local',
                'cards' => [
                    [
                        'title' => 'OpenAPI',
                        'desc'  => 'Utiliza el explorador OpenAPI integrado para inspeccionar esquemas, generar solicitudes de ejemplo y probar endpoints en el navegador, facilitando compartir y documentar tus flujos de pago con el equipo.',
                    ],
                    [
                        'title' => 'Postman',
                        'desc'  => 'Clona la colección de Postman para ejecutar flujos con scripts, parametrizar entornos y comparar rápidamente cómo se comportan distintos proveedores bajo la misma secuencia de solicitudes.',
                    ],
                    [
                        'title' => 'CI',
                        'desc'  => 'Integra el simulador en tus trabajos de integración continua para que cada rama y pull request se validen contra los mismos escenarios de pago y webhooks que esperas en producción.',
                    ],
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Providers (Catalog + Search)
    |--------------------------------------------------------------------------
    */
    'providers' => [
        'title'       => 'Proveedores',
        'description' => 'Explora los proveedores de pago compatibles, revisa los formatos de carga útil de sus webhooks y compara esquemas de firma lado a lado para diseñar una capa de integración coherente y agnóstica del proveedor.',
        'map' => [
            'mock'         => 'Mock',
            'xendit'       => 'Xendit',
            'midtrans'     => 'Midtrans',
            'stripe'       => 'Stripe',
            'paypal'       => 'PayPal',
            'paddle'       => 'Paddle',
            'lemonsqueezy' => 'Lemon Squeezy',
            'airwallex'    => 'Airwallex',
            'tripay'       => 'Tripay',
            'doku'         => 'DOKU',
            'dana'         => 'DANA',
            'oy'           => 'OY!',
            'payoneer'     => 'Payoneer',
            'skrill'       => 'Skrill',
            'amazon_bwp'   => 'Amazon BWP',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pluralization / Examples
    |--------------------------------------------------------------------------
    */
    'plurals' => [
        'tests' => '{0} Aún no hay pruebas definidas|{1} :count prueba automatizada|[2,*] :count pruebas',
        'items' => '{0} No hay elementos disponibles|{1} :count elemento|[2,*] :count elementos',
    ],
];
