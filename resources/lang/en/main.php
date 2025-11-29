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
    'home_title' => 'Home',
    'providers_title' => 'Providers',
    'features' => 'Features',
    'endpoints' => 'Endpoints',
    'signature' => 'Signature',
    'tooling' => 'Tooling',
    'openapi' => 'OpenAPI',
    'github' => 'GitHub',

    /*
    |--------------------------------------------------------------------------
    | Accessibility / ARIA
    |--------------------------------------------------------------------------
    */
    'aria' => [
        'primary_nav' => 'Primary navigation',
        'utility_nav' => 'Utility navigation',
        'toggle_menu' => 'Toggle main navigation menu',
        'toggle_theme' => 'Toggle light and dark theme',
        'skip_to_main' => 'Skip directly to the main content',
        'language' => 'Change interface language',
    ],

    /*
    |--------------------------------------------------------------------------
    | Footer / Legal
    |--------------------------------------------------------------------------
    */
    'terms' => 'Terms',
    'privacy' => 'Privacy',
    'cookies' => 'Cookies',
    'footer_demo' => 'Payment architecture demo environment for learning, testing, and explaining modern webhook-first flows.',
    'build' => 'Build',

    /*
    |--------------------------------------------------------------------------
    | SEO Defaults (override per page via layout/seo.blade.php)
    |--------------------------------------------------------------------------
    */
    'meta' => [
        'default_title' => 'Payment Webhook Simulator',
        'default_description' => 'Simulate real-world payment flows with idempotent operations, verified webhook signatures, event deduplication, and realistic retry/backoff behaviour — all without exposing or depending on live payment gateway credentials.',
        'default_image_alt' => 'Diagram of the Payment Webhook Simulator architecture showing payment providers, webhook callbacks, retries, and status updates.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Home (Landing)
    |--------------------------------------------------------------------------
    */
    'home' => [
        'title' => 'Payment Webhook Simulator',
        'description' => 'Stabilize your payment integration in a safe, production-like sandbox. Exercise Idempotency-Key handling, signature verification, event deduplication, and retry with backoff before you ever point requests at a real payment gateway.',
        'hero' => [
            'heading_prefix' => 'PWS',
            'heading_emph' => 'Idempotent',
            'lede' => 'Simulate incoming payment events from multiple providers, verify raw-body signatures, deduplicate noisy retries, and observe realistic backoff strategies end to end — without touching live credentials or production webhooks.',
            'cta_docs' => 'OpenAPI reference',
            'cta_features' => 'Explore the features',
            'cta_github' => 'View source on GitHub',
            'stats' => [
                'providers' => ['label' => 'Simulated providers'],
                'tests' => ['label' => 'Automated tests'],
                'openapi' => ['label' => 'Documented endpoints'],
            ],
            'chip' => 'POST : /api/webhooks/mock with a signed JSON payload',
            'simulate' => 'Simulate real payment webhooks with idempotency, signature checks, and retry logic — before you hit production.',
        ],
        'sections' => [
            'features' => [
                'title' => 'Features',
                'lede' => 'Harden and stabilize your payment integration with realistic, repeatable simulations that mirror how modern gateways send, sign, and retry webhook events in production.',
                'ship' => 'Ship safer payment flows without touching real gateway creds.',
                'items' => [
                    [
                        'title' => 'Idempotency',
                        'desc' => 'Exercise proper Idempotency-Key handling so duplicate requests, replays, and client retries resolve into a single, consistent payment record instead of corrupting state.',
                    ],
                    [
                        'title' => 'Signature Verification',
                        'desc' => 'Validate the raw request body and provider headers using HMAC, timestamps, and secrets, giving you a safe place to perfect your signature verification logic before going live.',
                    ],
                    [
                        'title' => 'Dedup & Retry',
                        'desc' => 'Simulate duplicate webhook deliveries and exponential backoff so you can design handlers that are idempotent, resilient, and safe to execute multiple times without side effects.',
                    ],
                    [
                        'title' => 'OpenAPI',
                        'desc' => 'Browse interactive OpenAPI documentation that describes every payment and webhook endpoint, complete with schemas, examples, and ready-to-run curl snippets.',
                    ],
                    [
                        'title' => 'Postman',
                        'desc' => 'Import the curated Postman collection to hit endpoints, tweak payloads, and rehearse error conditions from your favourite API client in just a few clicks.',
                    ],
                    [
                        'title' => 'CI Integration',
                        'desc' => 'Wire the simulator into your CI pipelines so tests, linters, and contract checks run on every push, catching integration regressions long before they reach production.',
                    ],
                ],
            ],
            'endpoints' => [
                'title' => 'Endpoints',
                'lede' => 'Define a small, realistic surface area: create payments, poll status, and receive provider-style webhooks with idempotent semantics.',
                'cards' => [
                    [
                        'title' => 'POST /api/payments',
                        'desc' => 'Create a new simulated payment record. This endpoint requires an Idempotency-Key header so you can verify how your application deduplicates repeated payment attempts.',
                    ],
                    [
                        'title' => 'GET /api/payments/{id}',
                        'desc' => 'Fetch the latest state for a specific payment, including status transitions and any associated webhook events that have been processed by the simulator.',
                    ],
                    [
                        'title' => 'POST /api/webhooks/{provider}',
                        'desc' => 'Receive webhook callbacks for a given provider (mock, xendit, midtrans, and more) using realistic payloads, headers, and signature schemes tailored to each integration.',
                    ],
                ],
            ],
            'providers' => [
                'title' => 'Providers',
                'lede' => 'Try realistic webhook flows from multiple payment gateways in a single sandbox, without touching live credentials.',
                'cta_all' => 'View all providers',
                'map' => [
                    'mock' => 'Mock',
                    'xendit' => 'Xendit',
                    'midtrans' => 'Midtrans',
                    'stripe' => 'Stripe',
                    'paypal' => 'PayPal',
                    'paddle' => 'Paddle',
                    'lemonsqueezy' => 'Lemon Squeezy',
                    'airwallex' => 'Airwallex',
                    'tripay' => 'Tripay',
                    'doku' => 'DOKU',
                    'dana' => 'DANA',
                    'oy' => 'OY!',
                    'payoneer' => 'Payoneer',
                    'skrill' => 'Skrill',
                    'amazon_bwp' => 'Amazon BWP',
                ],
            ],
            'signature' => [
                'title' => 'Signature Verification',
                'lede' => 'Verify raw webhook payloads with HMAC signatures, timestamps, and strict header checks.',
                'compare' => 'Compare the signature your provider sends with what you compute from the raw request body, shared secret, and timestamp — in constant time.',
                'reject' => 'Reject mismatched signatures and stale timestamps automatically.',
                'cards' => [
                    [
                        'title' => 'HMAC / Timestamp',
                        'desc' => 'Experiment with timestamped HMAC signatures that protect against replay attacks. Inspect how raw-body hashes, shared secrets, and signed headers combine to form a verifiable audit trail for each webhook event.',
                    ],
                    [
                        'title' => 'Header-based',
                        'desc' => 'Work with provider-specific headers and tokens, from simple bearer secrets to structured signature envelopes, to ensure your application rejects forged payloads while keeping legitimate events flowing.',
                    ],
                ],
            ],
            'tooling' => [
                'title' => 'Tooling',
                'work' => 'Works with your local dev stack',
                'cards' => [
                    [
                        'title' => 'OpenAPI',
                        'desc' => 'Use the built-in OpenAPI explorer to inspect schemas, generate example requests, and try endpoints in the browser, making it easy to share and document your payment flows with the rest of the team.',
                    ],
                    [
                        'title' => 'Postman',
                        'desc' => 'Clone the Postman collection to run scripted flows, parameterize environments, and quickly compare how different providers behave under the same sequence of requests.',
                    ],
                    [
                        'title' => 'CI',
                        'desc' => 'Integrate the simulator into your continuous integration jobs so every branch and pull request is validated against the same payment and webhook scenarios you expect in production.',
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
        'title' => 'Providers',
        'description' => 'Browse supported payment providers, inspect their webhook payload formats, and compare signature schemes side by side so you can design a consistent, provider-agnostic integration layer.',
        'map' => [
            'mock' => 'Mock',
            'xendit' => 'Xendit',
            'midtrans' => 'Midtrans',
            'stripe' => 'Stripe',
            'paypal' => 'PayPal',
            'paddle' => 'Paddle',
            'lemonsqueezy' => 'Lemon Squeezy',
            'airwallex' => 'Airwallex',
            'tripay' => 'Tripay',
            'doku' => 'DOKU',
            'dana' => 'DANA',
            'oy' => 'OY!',
            'payoneer' => 'Payoneer',
            'skrill' => 'Skrill',
            'amazon_bwp' => 'Amazon BWP',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pluralization / Examples
    |--------------------------------------------------------------------------
    */
    'plurals' => [
        'tests' => '{0} No tests defined yet|{1} :count automated test|[2,*] :count Test',
        'items' => '{0} No items available|{1} :count item|[2,*] :count items',
    ],
];
