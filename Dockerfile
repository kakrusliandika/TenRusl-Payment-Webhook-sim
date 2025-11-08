# ====== Build stage: Composer deps ======
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-scripts

# ====== Runtime stage ======
FROM php:8.3-cli-alpine

# Install required extensions & tools
RUN apk add --no-cache git unzip libzip-dev oniguruma-dev icu-dev \
 && docker-php-ext-install pdo pdo_sqlite intl

WORKDIR /app

# Copy app source
COPY . /app
# Copy vendor from build stage
COPY --from=vendor /app/vendor /app/vendor

# Ensure writable dirs
RUN mkdir -p storage bootstrap/cache database \
 && chmod -R 775 storage bootstrap/cache \
 && touch database/database.sqlite

# Render will inject PORT. Expose for clarity.
EXPOSE 8080

# Start: key, cache, migrate, background workers, serve
CMD sh -lc '\
  php -v; \
  [ -n "$APP_KEY" ] || php artisan key:generate --force || true; \
  php artisan config:cache || true; \
  php artisan route:cache || true; \
  if [ "$DB_CONNECTION" = "sqlite" ]; then touch database/database.sqlite; fi; \
  php artisan migrate --force || true; \
  (php artisan schedule:work &); \
  (php artisan queue:work --sleep=1 --tries=1 &); \
  php -S 0.0.0.0:${PORT:-8080} -t public \
'
