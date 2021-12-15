#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
  composer install
  bin/console doctrine:database:create --if-not-exists
  bin/console doctrine:schema:update --force
fi

exec docker-php-entrypoint "$@"
