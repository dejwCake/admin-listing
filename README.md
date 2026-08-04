# Admin Listing

AdminListing is a helper for building admin listings on your Eloquent models, turning typical HTTP requests into ready-to-use query results. It automatically handles common tasks like pagination, ordering, and search, and also supports translatable Eloquent models (see [Translatable Eloquent Models](https://docs.brackets.sk/#/translatable#make-your-model-translatable)).

This package is part of [Craftable](https://github.com/dejwCake/craftable) (`dejwCake/craftable`), an administration starter kit for Laravel 13, forked from [Craftable](https://github.com/BRACKETS-by-TRIAD/craftable) (`brackets/craftable`).

## Documentation
You can find full documentation at https://docs.getcraftable.com/#/admin-listing

## Issues
Where do I report issues?
If something is not working as expected, please open an issue in the main repository https://github.com/dejwCake/craftable.

## How to develop this project

### Composer

Update dependencies:
```shell
docker compose run -it --rm test composer update
```

Composer normalization:
```shell
docker compose run -it --rm php-qa composer normalize
```

### Run code analysis tools (php-qa)

PHP compatibility:
```shell
docker compose run -it --rm php-qa phpcs --standard=.phpcs.compatibility.xml --cache=.phpcs.cache
```

Code style:
```shell
docker compose run -it --rm php-qa phpcs -s --colors --extensions=php
```

Fix style issues:
```shell
docker compose run -it --rm php-qa phpcbf -s --colors --extensions=php
```

Static analysis (phpstan):
```shell
docker compose run -it --rm php-qa phpstan analyse --configuration=phpstan.neon
```

Mess detector (phpmd):
```shell
docker compose run -it --rm php-qa phpmd ./config,./src,./tests ansi phpmd.xml --suffixes php --baseline-file phpmd.baseline.xml
```

### Run tests

Run tests against mariadb:
```shell
docker compose run -it --rm -e DB_CONNECTION=mysql test ./vendor/bin/phpunit
```

Run tests against postgresql:
```shell
docker compose run -it --rm -e DB_CONNECTION=pgsql test ./vendor/bin/phpunit
```

Run tests with coverage:
```shell
docker compose run -it --rm test ./vendor/bin/phpunit --coverage-text
```

### Run the whole PHP suite

Run every PHP check and the test suite against both databases in sequence (stops at the first failure):
```shell
docker compose run -it --rm test composer update \
  && docker compose run -it --rm php-qa composer normalize \
  && docker compose run -it --rm php-qa phpcs --standard=.phpcs.compatibility.xml --cache=.phpcs.cache \
  && docker compose run -it --rm php-qa phpcs -s --colors --extensions=php \
  && docker compose run -it --rm php-qa phpstan analyse --configuration=phpstan.neon \
  && docker compose run -it --rm php-qa phpmd ./config,./src,./tests ansi phpmd.xml --suffixes php --baseline-file phpmd.baseline.xml \
  && docker compose run -it --rm -e DB_CONNECTION=mysql test ./vendor/bin/phpunit \
  && docker compose run -it --rm -e DB_CONNECTION=pgsql test ./vendor/bin/phpunit
```
