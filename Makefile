.PHONY: init test openapi up down logs

init:
	composer install
	cp .env.example .env || true
	php artisan key:generate || true
	php artisan migrate --force
	npm install

test:
	composer test

openapi:
	php artisan l5-swagger:generate

up:
	docker compose up -d

down:
	docker compose down

logs:
	docker compose logs -f
