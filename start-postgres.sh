#!/usr/bin/env sh
set -e

# Pastikan APP_KEY ada (pakai yang sudah diset di env kalau ada)
if [ -z "${APP_KEY:-}" ]; then
  php artisan key:generate --force >/dev/null 2>&1 || true
fi

# Paksa Postgres (Railway biasanya inject DATABASE_URL; DB_CONNECTION tetap kita set untuk Laravel)
export DB_CONNECTION="${DB_CONNECTION:-pgsql}"

# Default runtime safety (jangan paksa database queue/session kalau tabelnya belum disiapkan)
export QUEUE_CONNECTION="${QUEUE_CONNECTION:-sync}"
export SESSION_DRIVER="${SESSION_DRIVER:-file}"
export CACHE_STORE="${CACHE_STORE:-file}"

# Cache config/route (aman walau DB belum ready)
php artisan config:cache >/dev/null 2>&1 || true
php artisan route:cache >/dev/null 2>&1 || true

# Jalankan migrate dengan retry sampai DB ready
RETRIES=20
SLEEP=3
i=0

until php artisan migrate --force --no-interaction; do
  i=$((i+1))
  if [ "$i" -ge "$RETRIES" ]; then
    echo "Migration failed after $RETRIES attempts."
    exit 1
  fi
  echo "Waiting for database... ($i/$RETRIES)"
  sleep $SLEEP
done

# Worker opsional (akan jalan kalau kamu ubah QUEUE_CONNECTION jadi redis/database + tabel sudah siap)
# Jangan bikin boot gagal kalau worker error — makanya kita jalankan best-effort.
(php artisan schedule:work >/dev/null 2>&1 &) || true
(php artisan queue:work --sleep=1 --tries=1 >/dev/null 2>&1 &) || true

# Start app
export PORT="${PORT:-8080}"
echo "Starting Laravel on 0.0.0.0:$PORT"
php -S 0.0.0.0:$PORT -t public
