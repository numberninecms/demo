#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
    set -- php-fpm7 "$@"
fi

if [ "$1" = 'php-fpm7' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
    PHP_INI_RECOMMENDED="$PHP_INI_DIR/php.ini-production"
    if [ "$APP_ENV" != 'prod' ]; then
        PHP_INI_RECOMMENDED="$PHP_INI_DIR/php.ini-development"
    fi
    ln -sf "$PHP_INI_RECOMMENDED" "$PHP_INI_DIR/php.ini"

    mkdir -p var/cache var/log

    if [ ! -f .env.local ]; then
        echo 'DATABASE_URL=mysql://user:user@mysql:3306/numbernine_app?serverVersion=5.7' >.env.local
        make install
        jq '.extra.symfony.docker=true' tmp/composer.json >tmp/composer.tmp.json
        rm tmp/composer.json
        mv tmp/composer.tmp.json tmp/composer.json

        cp -Rp tmp/. .
        rm -Rf tmp/
    fi

    setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
    setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var
fi

exec docker-php-entrypoint "$@"
