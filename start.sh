#!/usr/bin/env sh
set -e

# Runtime APP_KEY: jika tidak ada, generate ephemeral (cukup untuk demo)
if [ -z "${APP_KEY:-}" ]; then
  export APP_KEY="$(php -r "echo 'base64:'.base64_encode(random_bytes(32));")"
fi

# Default ke SQLite kalau tidak diset
if [ "${DB_CONNECTION:-}" = "sqlite" ] || [ -z "${DB_CONNECTION:-}" ]; then
  export DB_CONNECTION="sqlite"
  mkdir -p database
  [ -f database/database.sqlite ] || touch database/database.sqlite
  export DB_DATABASE="${DB_DATABASE:-database/database.sqlite}"
fi

# Default runtime stabil untuk demo
export QUEUE_CONNECTION="${QUEUE_CONNECTION:-sync}"
export SESSION_DRIVER="${SESSION_DRIVER:-file}"
export CACHE_STORE="${CACHE_STORE:-file}"

# Cache & migrate (best-effort untuk demo)
php artisan config:cache >/dev/null 2>&1 || true
php artisan route:cache >/dev/null 2>&1 || true
php artisan migrate --force --no-interaction >/dev/null 2>&1 || true

# Background workers (best-effort)
(php artisan schedule:work >/dev/null 2>&1 &) || true
(php artisan queue:work --sleep=1 --tries=1 >/dev/null 2>&1 &) || true

# Start app
export PORT="${PORT:-8080}"
echo "Starting Laravel on 0.0.0.0:$PORT"
php -S 0.0.0.0:$PORT -t public
