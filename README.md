# Admin Listing

AdminListing is a helper that simplifies administration listing for your Eloquent models. It helps transforming a typical request to data. It can auto-handle all the basic stuff like pagination, ordering, search. It can handle also translatable eloquent models (see [Translatable Eloquent Models](https://docs.brackets.sk/#/translatable#make-your-model-translatable)).

You can find full documentation at https://docs.getcraftable.com/#/admin-listing

## Composer

To develop this package, you need to have composer installed. To run composer command use:
```shell
  docker compose run -it --rm test composer update
```

For composer normalization:
```shell
  docker compose run -it --rm php-qa composer normalize
```

## Run tests

To run tests use this docker environment.
```shell
  docker compose run -it --rm test vendor/bin/phpunit -d pcov.enabled=1
```

To switch between postgresql and mariadb change in `docker-compose.yml` DB_CONNECTION environmental variable:
```git
- DB_CONNECTION: pgsql
+ DB_CONNECTION: mysql
```

## Run code analysis tools

To be sure, that your code is clean, you can run code analysis tools. To do this, run:

For php compatibility:
```shell
  docker compose run -it --rm php-qa phpcs --standard=.phpcs.compatibility.xml --cache=.phpcs.cache
```

For code style:
```shell
  docker compose run -it --rm php-qa phpcs -s --colors --extensions=php
```

or to fix issues:
```shell
  docker compose run -it --rm php-qa phpcbf -s --colors --extensions=php
```

For static analysis:
```shell
  docker compose run -it --rm php-qa phpstan analyse --configuration=phpstan.neon
```

For mess detector:
```shell
  docker compose run -it --rm php-qa phpmd ./src,./install-stubs,./tests ansi phpmd.xml --suffixes php --baseline-file phpmd.baseline.xml
```

## Issues
Where do I report issues?
If something is not working as expected, please open an issue in the main repository https://github.com/BRACKETS-by-TRIAD/craftable.