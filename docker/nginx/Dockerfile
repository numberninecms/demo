ARG NGINX_VERSION=1.17

FROM nginx:${NGINX_VERSION}-alpine AS numbernine_demo_nginx

COPY docker/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /srv/app

#COPY --from=numbernine_demo_php /srv/app/public public/
