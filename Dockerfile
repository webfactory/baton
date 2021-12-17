FROM node:12-alpine3.12 as node_runner

WORKDIR /srv/app

COPY gulp-config.js .
COPY gulpfile.js .
COPY package.json .
COPY yarn.lock .

ENV NODE_ENV=production
RUN npm start

FROM php:7.4-fpm-alpine3.13 as symfony_php

ARG APCU_VERSION=5.1.21
ARG XDEBUG_VERSION=3.1.2

ENV LC_ALL POSIX

RUN apk add --no-cache \
        acl \
        fcgi \
        file \
        gettext \
        git \
        gnu-libiconv \
        npm \
        vim \
        zip

# install gnu-libiconv and set LD_PRELOAD env to make iconv work fully on Alpine image.
# see https://github.com/docker-library/php/issues/240#issuecomment-763112749
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so

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
        xdebug-${XDEBUG_VERSION} \
	; \
	pecl clear-cache; \
	docker-php-ext-enable \
		apcu \
		opcache \
        xdebug \
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

RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

WORKDIR /srv/app

COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY . .
COPY --from=node_runner /srv/app/node_modules .

RUN set -eux; \
	mkdir -p var/cache logs; \
    setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var; \
    setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var; \
    setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX logs; \
    setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX logs; \
	composer install; \
	chmod +x bin/console; \
    sync;

COPY docker/php/wait-for /usr/local/bin/
RUN chmod +x /usr/local/bin/wait-for

COPY docker/php/docker-entrypoint /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["/usr/local/bin/docker-entrypoint"]
CMD ["php-fpm"]

FROM nginx:stable-alpine as symfony_nginx

WORKDIR /srv/app

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=symfony_php /srv/app/www www/

ENV APP_ENV prod
