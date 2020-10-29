# From https://github.com/dunglas/symfony-docker/blob/master/Dockerfile

ARG PHP_VERSION=7.4
ARG CADDY_VERSION=2.1.1

FROM kreait/php:${PHP_VERSION}-fpm-dev AS numbernine_app_php

# persistent / runtime deps
RUN apk add --no-cache \
        php7-exif \
        php7-apcu \
    ;

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN ln -s $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
COPY docker/php/conf.d/numbernine.ini $PHP_INI_DIR/conf.d/numbernine.ini

RUN set -eux; \
	{ \
		echo '[www]'; \
		echo 'ping.path = /ping'; \
	} | tee /etc/php7/php-fpm.d/docker-healthcheck.conf

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

# install Symfony Flex globally to speed up download of Composer packages (parallelized prefetching)
RUN set -eux; \
	composer global require "symfony/flex" --prefer-dist --no-progress --no-suggest --classmap-authoritative; \
	composer clear-cache
ENV PATH="${PATH}:/root/.composer/vendor/bin"

WORKDIR /srv/app

COPY php .

RUN echo 'DATABASE_URL=mysql://root@localhost:3306/numbernine_app?serverVersion=5.7' >> .env.local
RUN make install

RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer run-script --no-dev post-install-cmd; sync
VOLUME /srv/app/var

COPY docker/php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]


FROM caddy:${CADDY_VERSION} AS numbernine_app_caddy

WORKDIR /srv/app

COPY --from=numbernine_app_php /srv/app/public public/
COPY docker/caddy/Caddyfile /etc/caddy/Caddyfile
