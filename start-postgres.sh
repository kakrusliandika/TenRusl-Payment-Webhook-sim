#!/usr/bin/env sh
set -eu

APP_ENV="${APP_ENV:-production}"

STRICT_BOOT="${STRICT_BOOT:-}"
if [ -z "${STRICT_BOOT}" ]; then
  if [ "${APP_ENV}" = "production" ]; then
    STRICT_BOOT="1"
  else
    STRICT_BOOT="0"
  fi
fi

fail() {
  echo "ERROR: $*" >&2
  exit 1
}

warn() {
  echo "WARN: $*" >&2
}

# -----------------------------------------
# 1) APP_KEY must be provided (no auto-gen)
# -----------------------------------------
if [ -z "${APP_KEY:-}" ]; then
  if [ "${APP_ENV}" = "production" ] || [ "${STRICT_BOOT}" = "1" ]; then
    fail "APP_KEY is required. Generate once during release (php artisan key:generate) and set it via env."
  fi

  if [ "${ALLOW_EPHEMERAL_APP_KEY:-0}" = "1" ]; then
    export APP_KEY="$(php -r "echo 'base64:'.base64_encode(random_bytes(32));")"
    warn "Using ephemeral APP_KEY (dev-only). Do not use this in production."
  else
    fail "APP_KEY is missing. Set APP_KEY or set ALLOW_EPHEMERAL_APP_KEY=1 (dev-only)."
  fi
fi

# Force Postgres as default for this entrypoint
export DB_CONNECTION="${DB_CONNECTION:-pgsql}"

# --------------------------------------------------------
# 2) Ensure Laravel writable directories (fail in prod)
# --------------------------------------------------------
ensure_dir() {
  d="$1"
  [ -d "$d" ] || mkdir -p "$d"

  if command -v id >/dev/null 2>&1 && [ "$(id -u)" = "0" ]; then
    chown -R www-data:www-data "$d" >/dev/null 2>&1 || true
  fi

  chmod -R ug+rwX "$d" >/dev/null 2>&1 || true

  if [ ! -w "$d" ]; then
    if [ "${APP_ENV}" = "production" ] || [ "${STRICT_BOOT}" = "1" ]; then
      fail "Directory not writable: $d"
    else
      warn "Directory not writable: $d"
    fi
  fi
}

ensure_dir "storage"
ensure_dir "bootstrap/cache"

# --------------------------------------------------------
# 3) Optimize caches (production default ON)
# --------------------------------------------------------
RUN_OPTIMIZE="${RUN_OPTIMIZE:-}"
if [ -z "${RUN_OPTIMIZE}" ]; then
  if [ "${APP_ENV}" = "production" ]; then
    RUN_OPTIMIZE="1"
  else
    RUN_OPTIMIZE="0"
  fi
fi

if [ "${RUN_OPTIMIZE}" = "1" ]; then
  php artisan config:cache --no-interaction
  php artisan route:cache --no-interaction || true
  php artisan view:cache --no-interaction || true
fi

# --------------------------------------------------------
# 4) Migrations are a RELEASE step (not at boot)
# --------------------------------------------------------
if [ "${RUN_MIGRATIONS:-0}" = "1" ]; then
  warn "RUN_MIGRATIONS=1 is enabled. Prefer running migrations as a separate release step."
  php artisan migrate --force --no-interaction
fi

# --------------------------------------------------------
# 5) Scheduler & Worker are separate processes
# --------------------------------------------------------
# Scheduler (separate container / cron):
#   * * * * * php /var/www/html/artisan schedule:run >> /dev/null 2>&1
#
# Worker (separate container / supervisor):
#   php artisan queue:work --sleep=1 --tries=1

# --------------------------------------------------------
# 6) Start the web process (one process per container)
# --------------------------------------------------------
START_MODE="${START_MODE:-fpm}" # fpm | builtin
PORT="${PORT:-8080}"

if [ "${START_MODE}" = "builtin" ]; then
  echo "Starting Laravel (builtin PHP server) on 0.0.0.0:${PORT}"
  exec php -S 0.0.0.0:"${PORT}" -t public
fi

echo "Starting PHP-FPM"
exec php-fpm -F
