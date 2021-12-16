#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
  composer install
  echo "Waiting for database server..."
  /usr/local/bin/wait-for --timeout 600 mysql:3306 -- echo "Database Server found"
  bin/console doctrine:database:create --if-not-exists
  bin/console doctrine:schema:update --force
fi

exec docker-php-entrypoint "$@"
