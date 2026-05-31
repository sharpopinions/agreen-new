APP = docker compose exec app

up:
	docker compose up -d

down:
	docker compose down

rebuild:
	docker compose down && docker compose build && docker compose up -d

ps:
	docker compose ps

logs:
	docker compose logs -f

logs-%:
	docker compose logs -f $*

shell:
	docker compose exec app bash

psql:
	docker compose exec postgres psql -U agreen agreen

migrate:
	$(APP) php artisan migrate

migrate-fresh:
	$(APP) php artisan migrate:fresh --seed

test:
	$(APP) php artisan test

test-filter:
	$(APP) php artisan test --filter=$(FILTER)

artisan:
	$(APP) php artisan $(CMD)

composer:
	$(APP) composer $(CMD)

dev:
	npm run dev

build:
	npm run build

npm:
	npm $(CMD)

.PHONY: up down rebuild ps logs shell psql migrate migrate-fresh test artisan composer dev build npm