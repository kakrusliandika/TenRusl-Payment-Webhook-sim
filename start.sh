#!/usr/bin/env sh
set -eu

MODE="${1:-web}" # web | fpm | worker | scheduler-run | scheduler-work

APP_ENV="${APP_ENV:-production}"

# Strict by default in production
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
    fail "APP_KEY is required. Generate once and set it via env/secret."
  fi

  # Optional for local/dev ONLY (explicit opt-in)
  if [ "${ALLOW_EPHEMERAL_APP_KEY:-0}" = "1" ]; then
    export APP_KEY="$(php -r "echo 'base64:'.base64_encode(random_bytes(32));")"
    warn "Using ephemeral APP_KEY (dev-only). Do not use this in production."
  else
    fail "APP_KEY is missing. Set APP_KEY or set ALLOW_EPHEMERAL_APP_KEY=1 (dev-only)."
  fi
fi

# --------------------------------------------------------
# 2) Ensure Laravel writable directories (fail in prod)
# --------------------------------------------------------
ensure_dir() {
  d="$1"
  [ -d "$d" ] || mkdir -p "$d"
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
# 3) Fail-fast: prevent silent “demo-mode” in production
# --------------------------------------------------------
if [ "${APP_ENV}" = "production" ] || [ "${STRICT_BOOT}" = "1" ]; then
  if [ "${DB_CONNECTION:-}" = "" ] || [ "${DB_CONNECTION}" = "sqlite" ]; then
    fail "Production must not use sqlite. Set DB_CONNECTION=pgsql/mysql and provide DB_URL (recommended) or DB_HOST/DB_*."
  fi

  if [ -z "${DB_URL:-}" ] && [ -z "${DB_HOST:-}" ]; then
    fail "Database config missing. Set DB_URL (recommended) or DB_HOST/DB_DATABASE/DB_USERNAME/DB_PASSWORD."
  fi

  if [ "${CACHE_STORE:-}" != "redis" ]; then
    fail "Production requires CACHE_STORE=redis (for locks/idempotency/multi-instance)."
  fi

  if [ "${QUEUE_CONNECTION:-}" != "redis" ]; then
    fail "Production requires QUEUE_CONNECTION=redis (for retry + burst traffic)."
  fi

  # Session tidak selalu kritikal utk webhook, tapi aman disentralisasi
  if [ "${SESSION_DRIVER:-}" != "redis" ]; then
    warn "SESSION_DRIVER is not redis. Recommended: SESSION_DRIVER=redis for multi-instance."
  fi
fi

# --------------------------------------------------------
# 4) Optimize caches (production default ON)
#    + Event cache (recommended when you have many events/listeners)
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

  # event:cache documented by Laravel (safe to no-op/fail-soft if not applicable)
  php artisan event:cache --no-interaction || true
fi

# --------------------------------------------------------
# 5) Migrations are a RELEASE step (not at boot)
# --------------------------------------------------------
if [ "${RUN_MIGRATIONS:-0}" = "1" ]; then
  warn "RUN_MIGRATIONS=1 is enabled. Prefer platform preDeployCommand / release step."
  php artisan migrate --force --no-interaction
fi

# --------------------------------------------------------
# Helper: graceful supervisor for long-running artisan
# --------------------------------------------------------
run_graceful() {
  # $1..$n = command
  "$@" &
  CHILD_PID="$!"

  trap 'kill -TERM "${CHILD_PID}" 2>/dev/null || true; wait "${CHILD_PID}" 2>/dev/null || true; exit 0' INT TERM
  wait "${CHILD_PID}"
}

# --------------------------------------------------------
# 6) Mode switch
# --------------------------------------------------------
case "${MODE}" in
  web)
    # nginx + php-fpm, nginx listens on $PORT
    PORT="${PORT:-10000}"
    CLIENT_MAX_BODY_SIZE="${CLIENT_MAX_BODY_SIZE:-5m}"
    FASTCGI_READ_TIMEOUT="${FASTCGI_READ_TIMEOUT:-120}"

    mkdir -p /etc/nginx/conf.d

    cat > /etc/nginx/conf.d/default.conf <<EOF
server {
  listen ${PORT};
  server_name _;
  root /var/www/html/public;
  index index.php;

  access_log /dev/stdout;
  error_log /dev/stderr warn;

  client_max_body_size ${CLIENT_MAX_BODY_SIZE};

  location / {
    try_files \$uri \$uri/ /index.php?\$query_string;
  }

  location ~ \.php\$ {
    include /etc/nginx/fastcgi_params;
    fastcgi_pass 127.0.0.1:9000;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
    fastcgi_param DOCUMENT_ROOT \$realpath_root;
    fastcgi_read_timeout ${FASTCGI_READ_TIMEOUT};
  }

  location ~ /\.(?!well-known).* {
    deny all;
  }
}
EOF

    php-fpm -F &
    FPM_PID="$!"

    nginx -g "daemon off;" &
    NGINX_PID="$!"

    trap 'kill -TERM "${FPM_PID}" "${NGINX_PID}" 2>/dev/null || true; wait || true' INT TERM

    # monitor: kalau salah satu mati, matikan yang lain
    while kill -0 "${FPM_PID}" 2>/dev/null && kill -0 "${NGINX_PID}" 2>/dev/null; do
      sleep 1
    done

    warn "One of the processes exited. Shutting down..."
    kill -TERM "${FPM_PID}" "${NGINX_PID}" 2>/dev/null || true
    wait || true
    exit 1
    ;;

  fpm)
    # php-fpm only (untuk compose + nginx terpisah)
    exec php-fpm -F
    ;;

  worker)
    WORKER_SLEEP="${WORKER_SLEEP:-1}"
    WORKER_TRIES="${WORKER_TRIES:-3}"
    WORKER_TIMEOUT="${WORKER_TIMEOUT:-90}"
    WORKER_QUEUE="${WORKER_QUEUE:-default}"

    # Graceful: forward SIGTERM/SIGINT to artisan worker
    run_graceful php artisan queue:work \
      --sleep="${WORKER_SLEEP}" \
      --tries="${WORKER_TRIES}" \
      --timeout="${WORKER_TIMEOUT}" \
      --queue="${WORKER_QUEUE}"
    ;;

  scheduler-run)
    # dipanggil cron job (sekali eksekusi)
    exec php artisan schedule:run --no-interaction
    ;;

  scheduler-work)
    # loop worker (enak utk local/dev)
    run_graceful php artisan schedule:work --no-interaction
    ;;

  *)
    fail "Unknown MODE: ${MODE}. Use: web | fpm | worker | scheduler-run | scheduler-work"
    ;;
esac
