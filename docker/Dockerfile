FROM numberninecms/php:8.1-fpm AS app_php

RUN apk add acl;

COPY docker/php/symfony.prod.ini /etc/php81/conf.d/100_symfony.ini

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

VOLUME /var/run/php

WORKDIR /srv/app
COPY --chown=82:82 . .

RUN set -eux; \
	mkdir -p var/cache var/log; \
	chmod +x bin/console;

VOLUME /srv/app/var

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm81"]


FROM nginx:alpine AS app_nginx

WORKDIR /srv/app

COPY --from=app_php /srv/app/public ./public/
