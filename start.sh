#!/usr/bin/env sh
set -e

# Runtime APP_KEY: if not set in env, generate ephemeral
if [ -z "$APP_KEY" ]; then
  export APP_KEY=$(php -r "echo 'base64:'.base64_encode(random_bytes(32));")
fi

# SQLite quick mode (optional). Touch DB file if using sqlite.
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
  export DB_CONNECTION="sqlite"
  mkdir -p database
  [ -f database/database.sqlite ] || touch database/database.sqlite
fi

# Cache & migrate
php artisan config:cache || true
php artisan route:cache || true
php artisan migrate --force || true

# Background workers (retry simulation & queues)
(php artisan schedule:work &) >/dev/null 2>&1
(php artisan queue:work --sleep=1 --tries=1 &) >/dev/null 2>&1

# Start the app
export PORT=${PORT:-8080}
echo "Starting Laravel on 0.0.0.0:$PORT"
php -S 0.0.0.0:$PORT -t public
