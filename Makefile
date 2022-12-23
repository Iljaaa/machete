build:
	docker-compose build

up:
	docker-compose up

composer-version:
	docker-compose run --rm php-cli composer --version

composer-dump-autoload:
	docker-compose run --rm php-cli composer dump-autoload

test:
	docker-compose run --rm php-cli ./vendor/bin/phpunit

