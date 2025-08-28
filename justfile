cli:
    @docker compose run --rm cli sh

composer-install:
    @docker compose run --rm cli composer install

test:
    @docker compose run --rm cli vendor/bin/phpunit

coverage:
    @docker compose run --rm cli vendor/bin/phpunit --coverage-clover=coverage.xml

phpstan:
    @docker compose run --rm cli vendor/bin/phpstan analyse --memory-limit 1G

pint:
    @docker compose run --rm cli vendor/bin/pint
