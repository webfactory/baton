FROM node:17.3-alpine3.12 as node_runner

ENV NODE_ENV=production
ENV PYTHONUNBUFFERED=1

WORKDIR /srv/app

COPY gulp-config.js .
COPY gulpfile.js .
COPY package.json .
COPY yarn.lock .

RUN set -eux; \
    apk add --update --no-cache \
      g++ \
      make \
      python3; \
    ln -sf python3 /usr/bin/python; \
    python3 -m ensurepip; \
    pip3 install --no-cache --upgrade pip setuptools

RUN npm start

FROM php:7.4-fpm-alpine3.13 as symfony_php

ARG APCU_VERSION=5.1.21
ARG APCU_BC_VERSION=1.0.5

ENV LC_ALL POSIX

# install gnu-libiconv and set LD_PRELOAD env to make iconv work fully on Alpine image.
# see https://github.com/docker-library/php/issues/240#issuecomment-763112749
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so

WORKDIR /srv/app

COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY . .
COPY --from=node_runner /srv/app/node_modules .
COPY docker/php/zz-apcu_bc.ini /usr/local/etc/php/conf.d/
COPY docker/php/wait-for /usr/local/bin/
COPY docker/php/docker-entrypoint /usr/local/bin/

RUN set -eux; \
    cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini; \
    chmod +x /usr/local/bin/docker-entrypoint; \
    chmod +x /usr/local/bin/wait-for

RUN apk add --no-cache \
        acl \
        fcgi \
        file \
        gettext \
        git \
        gnu-libiconv \
        vim \
        zip

RUN set -eux; \
    apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        icu-dev \
        libzip-dev \
        zlib-dev \
    ; \
    \
    docker-php-ext-configure zip; \
    docker-php-ext-install -j$(nproc) \
        intl \
        zip \
        pdo \
        pdo_mysql \
    ; \
    pecl install \
        apcu-${APCU_VERSION} \
        apcu_bc-${APCU_BC_VERSION} \
    ; \
    pecl clear-cache; \
    docker-php-ext-enable \
        apcu \
    ; \
    \
    runDeps="$( \
        scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
            | tr ',' '\n' \
            | sort -u \
            | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
    )"; \
    apk add --no-cache --virtual .phpexts-rundeps $runDeps; \
    \
    apk del .build-deps

RUN set -eux; \
    mkdir -p var/cache logs; \
    setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var; \
    setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var; \
    setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX logs; \
    setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX logs; \
    composer install; \
    chmod +x bin/console; \
    sync;

ENTRYPOINT ["/usr/local/bin/docker-entrypoint"]
CMD ["php-fpm"]

FROM nginx:stable-alpine as symfony_nginx

WORKDIR /srv/app

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=symfony_php /srv/app/www www/
