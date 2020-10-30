ARG CADDY_VERSION=2.1.1

FROM caddy:${CADDY_VERSION} AS numbernine_app_caddy

WORKDIR /srv/app
COPY docker/caddy/Caddyfile /etc/caddy/Caddyfile
