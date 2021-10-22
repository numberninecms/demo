#!/bin/sh
set -e

if [ "${1#-}" != "$1" ]; then
  set -- php "$@"
fi

if [ "$1" = 'php-fpm81' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
  bin/console cache:clear --no-debug
  setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
  setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var
fi

exec "$@"
