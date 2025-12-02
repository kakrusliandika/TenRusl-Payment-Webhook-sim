.PHONY: init test lint analyse docs dev up down logs

PHP ?= php
COMPOSER ?= composer
NPM ?= npm
ARTISAN = $(PHP) artisan

# Inisialisasi lokal cepat
init:
	$(COMPOSER) install
	cp .env.example .env || true
	$(ARTISAN) key:generate || true
	$(ARTISAN) migrate --force
	$(NPM) install

# Jalankan test suite (Pest)
test:
	$(COMPOSER) test

# Lint/format (Pint) — sesuaikan dengan script composer kamu
lint:
	$(COMPOSER) format:check

# Static analysis (Larastan/PHPStan) — sesuaikan dengan script composer kamu
analyse:
	$(COMPOSER) analyse:larastan

# Sinkronisasi docs OpenAPI (Redocly bundle + Postman)
docs:
	$(NPM) run docs:sync

# Dev server (Vite)
dev:
	$(NPM) run dev

# Docker (default pakai docker-compose.yml di root)
up:
	docker compose up -d

down:
	docker compose down

logs:
	docker compose logs -f
