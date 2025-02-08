# Admin Listing

AdminListing is a helper that simplifies administration listing for your Eloquent models. It helps transforming a typical request to data. It can auto-handle all the basic stuff like pagination, ordering, search. It can handle also translatable eloquent models (see [Translatable Eloquent Models](https://docs.brackets.sk/#/translatable#make-your-model-translatable)).

You can find full documentation at https://docs.getcraftable.com/#/admin-listing

## Run tests

To run tests use this docker environment.

```shell
  docker compose run -it --rm test vendor/bin/phpunit
```

To switch between postgresql and mariadb change in `docker-compose.yml` DB_CONNECTION environmental variable:

```git
- DB_CONNECTION: pgsql
+ DB_CONNECTION: mysql
```

## Issues
Where do I report issues?
If something is not working as expected, please open an issue in the main repository https://github.com/BRACKETS-by-TRIAD/craftable.