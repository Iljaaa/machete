build:
	docker-compose build

up:
	docker-compose up

composer-version:
	docker-compose run --rm php-cli composer --version

test:
	docker-compose run --rm php-cli ./vendor/bin/phpunit --version

