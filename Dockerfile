# ====== Stage: vendor deps (Composer) ======
FROM composer:2 AS vendor
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-progress

# ====== Stage: runtime (PHP-FPM) ======
FROM php:8.3-fpm-alpine

# System deps untuk ekstensi PHP umum (Laravel)
RUN apk add --no-cache \
      bash \
      git \
      unzip \
      icu-dev \
      libzip-dev \
  && docker-php-ext-install \
      pdo \
      pdo_mysql \
      pdo_sqlite \
      intl \
      zip \
  && rm -rf /var/cache/apk/*

# Copy composer binary ke runtime supaya bisa dipakai di container (compose command)
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

# Copy source (untuk image build tanpa bind-mount)
COPY . /var/www/html

# Copy vendor hasil composer stage
COPY --from=vendor /app/vendor /var/www/html/vendor

# Pastikan direktori writable Laravel
RUN mkdir -p storage bootstrap/cache database \
 && chown -R www-data:www-data storage bootstrap/cache database \
 && chmod -R 775 storage bootstrap/cache \
 && touch database/database.sqlite || true

EXPOSE 9000
CMD ["php-fpm"]
