up:
	docker compose up -d

up_build:
	docker compose up -d --build

bash:
	docker compose exec php bash

db:
	docker compose exec mysql bash

composer_install:
	docker compose exec -T php bash -c "composer install"

reset_db:
	docker compose exec -T php bash -c "php bin/doctrine orm:schema-tool:drop --force"

migrate:
	docker compose exec -T php bash -c "php bin/doctrine orm:schema-tool:update --force"

fixtures:
	docker compose exec -T php bash -c "php bin/fixtures"

setup:
	up
	composer_install
	migrate
	fixtures

down:
	docker compose down

