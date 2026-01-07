<?php

use Illuminate\Support\Str;

$defaultConnection = env('DB_CONNECTION', 'sqlite');

$appEnv = strtolower((string) env('APP_ENV', 'production'));
$isProduction = $appEnv === 'production';

// Fail-fast: jangan biarkan sqlite "kebawa" ke production tanpa sengaja.
// Escape hatch (kalau benar-benar butuh): ALLOW_SQLITE_IN_PRODUCTION=1
$allowSqliteInProduction = filter_var(env('ALLOW_SQLITE_IN_PRODUCTION', false), FILTER_VALIDATE_BOOL);

if ($isProduction && strtolower((string) $defaultConnection) === 'sqlite' && !$allowSqliteInProduction) {
    throw new RuntimeException('Misconfiguration: DB_CONNECTION=sqlite is not allowed in production. Use pgsql/mysql and set DB_URL or DB_HOST/DB_*.');
}

/**
 * MySQL / MariaDB SSL Options (PDO)
 * - Set via env to enable TLS to managed DB.
 * - Keep false values (e.g. VERIFY_SERVER_CERT=false) if explicitly set.
 */
$mysqlSslOptions = [];
$mysqlSslCa = env('MYSQL_ATTR_SSL_CA');
if ($mysqlSslCa !== null && $mysqlSslCa !== '') {
    $mysqlSslOptions[PDO::MYSQL_ATTR_SSL_CA] = $mysqlSslCa;
}
$mysqlSslCapath = env('MYSQL_ATTR_SSL_CAPATH');
if ($mysqlSslCapath !== null && $mysqlSslCapath !== '') {
    $mysqlSslOptions[PDO::MYSQL_ATTR_SSL_CAPATH] = $mysqlSslCapath;
}
$mysqlSslCert = env('MYSQL_ATTR_SSL_CERT');
if ($mysqlSslCert !== null && $mysqlSslCert !== '') {
    $mysqlSslOptions[PDO::MYSQL_ATTR_SSL_CERT] = $mysqlSslCert;
}
$mysqlSslKey = env('MYSQL_ATTR_SSL_KEY');
if ($mysqlSslKey !== null && $mysqlSslKey !== '') {
    $mysqlSslOptions[PDO::MYSQL_ATTR_SSL_KEY] = $mysqlSslKey;
}
$mysqlSslCipher = env('MYSQL_ATTR_SSL_CIPHER');
if ($mysqlSslCipher !== null && $mysqlSslCipher !== '') {
    $mysqlSslOptions[PDO::MYSQL_ATTR_SSL_CIPHER] = $mysqlSslCipher;
}
$mysqlVerifyServerCert = env('MYSQL_ATTR_SSL_VERIFY_SERVER_CERT');
if ($mysqlVerifyServerCert !== null && $mysqlVerifyServerCert !== '') {
    $mysqlSslOptions[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = filter_var($mysqlVerifyServerCert, FILTER_VALIDATE_BOOL);
}

return [
    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for database operations. This is
    | the connection which will be utilized unless another connection
    | is explicitly specified when you execute a query / statement.
    |
    */

    'default' => $defaultConnection,

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Below are all of the database connections defined for your application.
    | An example configuration is provided for each database system which
    | is supported by Laravel. You're free to add / remove connections.
    |
    */

    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DB_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
            'busy_timeout' => null,
            'journal_mode' => null,
            'synchronous' => null,
            'transaction_mode' => 'DEFERRED',
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,

            // SSL / TLS for managed MySQL (RDS, PlanetScale, etc.)
            'options' => extension_loaded('pdo_mysql') ? $mysqlSslOptions : [],
        ],

        'mariadb' => [
            'driver' => 'mariadb',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,

            // SSL / TLS for managed MariaDB
            'options' => extension_loaded('pdo_mysql') ? $mysqlSslOptions : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => env('DB_SEARCH_PATH', 'public'),

            // SSL for managed Postgres (Render, Supabase, RDS, etc.)
            // Common values: disable | allow | prefer | require | verify-ca | verify-full
            'sslmode' => env('DB_SSLMODE', 'prefer'),
            'sslrootcert' => env('DB_SSLROOTCERT'),
            'sslcert' => env('DB_SSLCERT'),
            'sslkey' => env('DB_SSLKEY'),
            'sslcrl' => env('DB_SSLCRL'),
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            // 'encrypt' => env('DB_ENCRYPT', 'yes'),
            // 'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', 'false'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run on the database.
    |
    */

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as Memcached. You may define your connection settings here.
    |
    */

    'redis' => [
        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),

            // Prefix dibuat environment-aware supaya tidak tabrakan antar env.
            'prefix' => env(
                'REDIS_PREFIX',
                Str::slug((string) env('APP_NAME', 'laravel')).'-'.Str::slug((string) env('APP_ENV', 'production')).'-database-'
            ),

            'persistent' => env('REDIS_PERSISTENT', false),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),

            // Default Redis DB index
            'database' => env('REDIS_DB', '0'),

            'max_retries' => env('REDIS_MAX_RETRIES', 3),
            'backoff_algorithm' => env('REDIS_BACKOFF_ALGORITHM', 'decorrelated_jitter'),
            'backoff_base' => env('REDIS_BACKOFF_BASE', 100),
            'backoff_cap' => env('REDIS_BACKOFF_CAP', 1000),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),

            // Cache Redis DB index (dipisah supaya keys rapi)
            'database' => env('REDIS_CACHE_DB', '1'),

            'max_retries' => env('REDIS_MAX_RETRIES', 3),
            'backoff_algorithm' => env('REDIS_BACKOFF_ALGORITHM', 'decorrelated_jitter'),
            'backoff_base' => env('REDIS_BACKOFF_BASE', 100),
            'backoff_cap' => env('REDIS_BACKOFF_CAP', 1000),
        ],

        // Queue Redis DB index (pisah dari cache untuk menghindari collision)
        'queue' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_QUEUE_DB', '2'),
            'max_retries' => env('REDIS_MAX_RETRIES', 3),
            'backoff_algorithm' => env('REDIS_BACKOFF_ALGORITHM', 'decorrelated_jitter'),
            'backoff_base' => env('REDIS_BACKOFF_BASE', 100),
            'backoff_cap' => env('REDIS_BACKOFF_CAP', 1000),
        ],

        // Lock Redis DB index (opsional; sangat disarankan jika app banyak lock)
        'lock' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_LOCK_DB', '3'),
            'max_retries' => env('REDIS_MAX_RETRIES', 3),
            'backoff_algorithm' => env('REDIS_BACKOFF_ALGORITHM', 'decorrelated_jitter'),
            'backoff_base' => env('REDIS_BACKOFF_BASE', 100),
            'backoff_cap' => env('REDIS_BACKOFF_CAP', 1000),
        ],
    ],
];
