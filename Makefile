.PHONY: setup start stop test analyse format check fixtures

setup:
	docker compose up -d --wait
	composer install
	npm ci
	npm run build
	php bin/console doctrine:migrations:migrate --no-interaction
	php bin/console doctrine:fixtures:load --no-interaction
	php bin/console fos:elastica:populate

start:
	docker compose up -d --wait

stop:
	docker compose down

test:
	composer test

analyse:
	composer analyse

format:
	composer format

check:
	composer check
	npm audit
	npm run build

fixtures:
	php bin/console doctrine:fixtures:load --no-interaction
	php bin/console fos:elastica:populate
