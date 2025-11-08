#!/usr/bin/env sh
set -e

# Ensure APP_KEY (use existing if provided)
if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force || true
fi

# Force Postgres
export DB_CONNECTION=${DB_CONNECTION:-pgsql}

# Cache config & routes
php artisan config:cache || true
php artisan route:cache || true

# Ensure queue/session use database (optional but recommended for demo)
export QUEUE_CONNECTION=${QUEUE_CONNECTION:-database}
export SESSION_DRIVER=${SESSION_DRIVER:-database}
export CACHE_STORE=${CACHE_STORE:-database}

# Run migrations with retries while Postgres becomes ready
RETRIES=20
SLEEP=3
i=0
until php artisan migrate --force; do
  i=$((i+1))
  if [ "$i" -ge "$RETRIES" ]; then
    echo "Migration failed after $RETRIES attempts."
    exit 1
  fi
  echo "Waiting for database... ($i/$RETRIES)"
  sleep $SLEEP
done

# Background workers (retry simulation & queues)
(php artisan schedule:work &) >/dev/null 2>&1
(php artisan queue:work --sleep=1 --tries=1 &) >/dev/null 2>&1

# Start the app
export PORT=${PORT:-8080}
echo "Starting Laravel on 0.0.0.0:$PORT"
php -S 0.0.0.0:$PORT -t public
