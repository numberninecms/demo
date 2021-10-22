#!/bin/sh
set -e

if [ "${1#-}" != "$1" ]; then
  set -- php "$@"
fi

if [ "$1" = 'php-fpm81' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
  bin/console cache:clear --no-debug

  if ls -A migrations/*.php >/dev/null 2>&1; then
    if grep -q ^DATABASE_URL= .env.local; then
      echo "Waiting for db to be ready..."
      ATTEMPTS_LEFT_TO_REACH_DATABASE=60
      until [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ] || DATABASE_ERROR=$(bin/console dbal:run-sql "SELECT 1" 2>&1); do
        if [ $? -eq 255 ]; then
          # If the Doctrine command exits with 255, an unrecoverable error occurred
          ATTEMPTS_LEFT_TO_REACH_DATABASE=0
          break
        fi
        sleep 1
        ATTEMPTS_LEFT_TO_REACH_DATABASE=$((ATTEMPTS_LEFT_TO_REACH_DATABASE - 1))
        echo "Still waiting for db to be ready... Or maybe the db is not reachable. $ATTEMPTS_LEFT_TO_REACH_DATABASE attempts left"
      done

      if [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ]; then
        echo "The database is not up or not reachable:"
        echo "$DATABASE_ERROR"
        exit 1
      else
        echo "The db is now ready and reachable"
      fi
		fi

    bin/console doctrine:migrations:migrate --no-interaction
  fi

  setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
  setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var
fi

exec "$@"
