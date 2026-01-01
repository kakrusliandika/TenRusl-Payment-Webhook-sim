# syntax=docker/dockerfile:1.7

ARG PHP_VERSION=8.3

# ==========================
# Stage: vendor (production deps only)
# ==========================
FROM composer:2 AS vendor
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
      --no-dev \
      --prefer-dist \
      --no-interaction \
      --no-progress \
      --optimize-autoloader \
  && composer clear-cache

# ==========================
# Stage: base runtime
# ==========================
FROM php:${PHP_VERSION}-fpm-alpine AS base
WORKDIR /var/www/html

# Runtime libs + build deps (temporary)
RUN apk add --no-cache \
      icu-libs \
      libzip \
      oniguruma \
  && apk add --no-cache --virtual .build-deps \
      $PHPIZE_DEPS \
      icu-dev \
      libzip-dev \
      oniguruma-dev \
  && docker-php-ext-install -j"$(nproc)" \
      pdo_mysql \
      pdo_sqlite \
      mbstring \
      intl \
      zip \
      bcmath \
      pcntl \
      opcache \
  && apk del .build-deps

# PHP baseline (safe defaults; Nginx handles client_max_body_size)
RUN { \
      echo "expose_php=0"; \
      echo "memory_limit=256M"; \
      echo "max_execution_time=60"; \
      echo "post_max_size=10M"; \
      echo "upload_max_filesize=10M"; \
    } > /usr/local/etc/php/conf.d/zz-tenrusl.ini

# Copy app source
COPY . /var/www/html

# Copy vendor from vendor stage
COPY --from=vendor /app/vendor /var/www/html/vendor

# Laravel writable dirs
RUN mkdir -p storage bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

EXPOSE 9000

# ==========================
# Stage: prod (immutable, non-dev)
# ==========================
FROM base AS prod

# OPcache (prod tuned). Enable CLI too (queue/scheduler run on CLI).
RUN { \
      echo "opcache.enable=1"; \
      echo "opcache.enable_cli=1"; \
      echo "opcache.memory_consumption=128"; \
      echo "opcache.interned_strings_buffer=16"; \
      echo "opcache.max_accelerated_files=20000"; \
      echo "opcache.validate_timestamps=0"; \
      echo "opcache.revalidate_freq=0"; \
      echo "opcache.jit=0"; \
    } > /usr/local/etc/php/conf.d/opcache.ini

CMD ["php-fpm"]

# ==========================
# Stage: dev (bind-mount friendly)
# ==========================
FROM base AS dev

# Keep timestamps validation ON for hot reload in dev
RUN { \
      echo "opcache.enable=1"; \
      echo "opcache.enable_cli=1"; \
      echo "opcache.memory_consumption=128"; \
      echo "opcache.interned_strings_buffer=16"; \
      echo "opcache.max_accelerated_files=20000"; \
      echo "opcache.validate_timestamps=1"; \
      echo "opcache.revalidate_freq=1"; \
      echo "opcache.jit=0"; \
    } > /usr/local/etc/php/conf.d/opcache.ini

# Composer in dev only (optional convenience)
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

CMD ["php-fpm"]
