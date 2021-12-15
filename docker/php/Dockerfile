FROM php:7.4-fpm-alpine3.13

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
        nodejs-current \
        npm \
        vim \
        zip \
    && \
    npm i -g corepack

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

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=symfonycorp/cli /symfony /usr/bin/symfony
COPY --from=oskarstark/php-cs-fixer-ga /usr/local/bin/php-cs-fixer /usr/bin/php-cs-fixer

COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php-fpm"]

WORKDIR /srv/app
