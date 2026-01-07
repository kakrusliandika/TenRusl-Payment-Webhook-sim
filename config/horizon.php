<?php

use Illuminate\Support\Str;

return [
    /*
    |--------------------------------------------------------------------------
    | Horizon Domain
    |--------------------------------------------------------------------------
    |
    | This is the subdomain where Horizon will be accessible from. If this
    | value is null, Horizon will reside under the same domain as the app.
    |
    */

    'domain' => env('HORIZON_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | Horizon Path
    |--------------------------------------------------------------------------
    |
    | This is the URI path where Horizon will be accessible from. Feel free
    | to change this path to anything you like.
    |
    */

    'path' => env('HORIZON_PATH', 'horizon'),

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Connection
    |--------------------------------------------------------------------------
    |
    | This option determines the Redis connection Horizon will use.
    | IMPORTANT: The connection name "horizon" is reserved by Horizon.
    |
    */

    'use' => env('HORIZON_REDIS_CONNECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix for all Horizon data in Redis.
    |
    */

    'prefix' => env(
        'HORIZON_PREFIX',
        Str::slug((string) env('APP_NAME', 'laravel'), '_').'_horizon:'
    ),

    /*
    |--------------------------------------------------------------------------
    | Horizon Route Middleware
    |--------------------------------------------------------------------------
    |
    | Middleware applied to Horizon routes.
    |
    */

    'middleware' => explode(',', (string) env('HORIZON_MIDDLEWARE', 'web')),

    /*
    |--------------------------------------------------------------------------
    | Wait Time Thresholds
    |--------------------------------------------------------------------------
    |
    | Configure how long (seconds) a job can wait in a queue before Horizon
    | fires a LongWaitDetected event.
    |
    */

    'waits' => [
        'redis:default' => (int) env('HORIZON_WAIT_DEFAULT', 60),
        'redis:webhooks' => (int) env('HORIZON_WAIT_WEBHOOKS', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Job Trimming Times
    |--------------------------------------------------------------------------
    |
    | How long (minutes) Horizon should keep recent / failed / completed jobs.
    |
    */

    'trim' => [
        'recent' => (int) env('HORIZON_TRIM_RECENT', 60),
        'pending' => (int) env('HORIZON_TRIM_PENDING', 60),
        'completed' => (int) env('HORIZON_TRIM_COMPLETED', 60),
        'recent_failed' => (int) env('HORIZON_TRIM_RECENT_FAILED', 10080), // 7d
        'failed' => (int) env('HORIZON_TRIM_FAILED', 10080),               // 7d
        'monitored' => (int) env('HORIZON_TRIM_MONITORED', 10080),         // 7d
    ],

    /*
    |--------------------------------------------------------------------------
    | Silenced Jobs
    |--------------------------------------------------------------------------
    |
    | Jobs that should not appear in the completed jobs list.
    |
    */

    'silenced' => [
    ],

    /*
    |--------------------------------------------------------------------------
    | Metrics
    |--------------------------------------------------------------------------
    */

    'metrics' => [
        'trim_snapshots' => [
            'job' => (int) env('HORIZON_METRICS_TRIM_JOB_HOURS', 24),
            'queue' => (int) env('HORIZON_METRICS_TRIM_QUEUE_HOURS', 24),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Fast Termination
    |--------------------------------------------------------------------------
    |
    | Enables faster deployments by terminating Horizon quickly.
    |
    */

    'fast_termination' => (bool) env('HORIZON_FAST_TERMINATION', true),

    /*
    |--------------------------------------------------------------------------
    | Master Supervisor Memory Limit
    |--------------------------------------------------------------------------
    */

    'memory_limit' => (int) env('HORIZON_MEMORY_LIMIT', 128),

    /*
    |--------------------------------------------------------------------------
    | Default Supervisor Configuration
    |--------------------------------------------------------------------------
    |
    | These options apply to all environments unless overridden below.
    |
    */

    'defaults' => [
        // General / default queue worker
        'supervisor-default' => [
            'connection' => 'redis',
            'queue' => ['default'],
            'balance' => 'auto',
            'autoScalingStrategy' => env('HORIZON_AUTOSCALE_STRATEGY', 'time'),

            'minProcesses' => (int) env('HORIZON_DEFAULT_MIN_PROCESSES', 1),
            'maxProcesses' => (int) env('HORIZON_DEFAULT_MAX_PROCESSES', 10),

            'balanceMaxShift' => (int) env('HORIZON_DEFAULT_BALANCE_MAX_SHIFT', 1),
            'balanceCooldown' => (int) env('HORIZON_DEFAULT_BALANCE_COOLDOWN', 3),

            'tries' => (int) env('WORKER_TRIES', 3),
            'timeout' => (int) env('WORKER_TIMEOUT', 90),

            'memory' => (int) env('HORIZON_WORKER_MEMORY', 256),
            'nice' => (int) env('HORIZON_WORKER_NICE', 0),
        ],

        // Webhooks queue worker
        'supervisor-webhooks' => [
            'connection' => 'redis',
            'queue' => ['webhooks', 'default'],
            'balance' => 'auto',
            'autoScalingStrategy' => env('HORIZON_WEBHOOKS_AUTOSCALE_STRATEGY', env('HORIZON_AUTOSCALE_STRATEGY', 'time')),

            'minProcesses' => (int) env('HORIZON_WEBHOOKS_MIN_PROCESSES', 1),
            'maxProcesses' => (int) env('HORIZON_WEBHOOKS_MAX_PROCESSES', 20),

            'balanceMaxShift' => (int) env('HORIZON_WEBHOOKS_BALANCE_MAX_SHIFT', 2),
            'balanceCooldown' => (int) env('HORIZON_WEBHOOKS_BALANCE_COOLDOWN', 3),

            'tries' => (int) env('HORIZON_WEBHOOKS_TRIES', (int) env('WORKER_TRIES', 3)),
            'timeout' => (int) env('HORIZON_WEBHOOKS_TIMEOUT', (int) env('WORKER_TIMEOUT', 90)),

            'memory' => (int) env('HORIZON_WEBHOOKS_MEMORY', (int) env('HORIZON_WORKER_MEMORY', 256)),
            'nice' => (int) env('HORIZON_WEBHOOKS_NICE', (int) env('HORIZON_WORKER_NICE', 0)),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Environment Specific Supervisors
    |--------------------------------------------------------------------------
    */

    'environments' => [
        'production' => [
            'supervisor-default' => [
                'minProcesses' => (int) env('HORIZON_DEFAULT_MIN_PROCESSES', 2),
                'maxProcesses' => (int) env('HORIZON_DEFAULT_MAX_PROCESSES', 20),
            ],
            'supervisor-webhooks' => [
                'minProcesses' => (int) env('HORIZON_WEBHOOKS_MIN_PROCESSES', 2),
                'maxProcesses' => (int) env('HORIZON_WEBHOOKS_MAX_PROCESSES', 40),
            ],
        ],

        'local' => [
            'supervisor-default' => [
                'minProcesses' => 1,
                'maxProcesses' => 3,
            ],
            'supervisor-webhooks' => [
                'minProcesses' => 1,
                'maxProcesses' => 3,
            ],
        ],

        '*' => [
            // fallback untuk environment lain (staging, preview, dll)
        ],
    ],
];
