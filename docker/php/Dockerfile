ARG PHP_VERSION
FROM php:${PHP_VERSION}-fpm

ENV DEBIAN_FRONTEND noninteractive

WORKDIR /srv/app

# non-root user
ARG PUID=1000
ENV PUID ${PUID}
ARG PGID=1000
ENV PGID ${PGID}

RUN addgroup -gid ${PGID} docksy && \
  adduser -gid 1000 -uid ${PUID} --disabled-password docksy

# locales
ARG LOCALE=POSIX
ENV LC_ALL ${LOCALE}

# essentials
RUN apt-get update && pecl channel-update pecl.php.net &&\
    apt-get install -y \
        libicu-dev \
        libzip-dev \
        libjpeg-dev \
        libpng-dev \
        apt-utils \
        git \
        vim \
        zip &&\
    docker-php-ext-install \
        intl \
        opcache \
        pdo \
        pdo_mysql \
        zip

# imagemagick
RUN apt-get install -y libmagickwand-dev imagemagick && pecl install imagick && docker-php-ext-enable imagick

# apcu
RUN pecl install apcu && docker-php-ext-enable apcu

# xdebug
RUN pecl install xdebug-3.0.2 && docker-php-ext-enable xdebug

# node
RUN curl -sS https://deb.nodesource.com/setup_lts.x | bash - && apt-get install -yqq nodejs

RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=symfonycorp/cli /symfony /usr/bin/symfony
COPY --from=oskarstark/php-cs-fixer-ga /usr/local/bin/php-cs-fixer /usr/bin/php-cs-fixer

# RUN composer install && bin/console doctrine:database:create --if-not-exists && bin/console doctrine:schema:update && npm install && gulp compile
