# Get the Composer executable from the official Composer image.
FROM composer:1.6 as composer

# Run Baton in a small PHP container.
FROM php:7.2

RUN apt-get update

# Install dockerize to be able to wait for the MySQL server.
RUN apt-get install -y wget
ENV DOCKERIZE_VERSION v0.6.1
RUN wget https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && tar -C /usr/local/bin -xzvf dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && rm dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz

# Install Composer and required extensions.
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN apt-get install -y git zlib1g-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# install npm from nodesource using apt-get
RUN apt-get install -my wget gnupg \
    && curl -sL https://deb.nodesource.com/setup_8.x | bash - \
    && apt-get install -yq nodejs build-essential

RUN npm install -g npm && npm install -g gulp


WORKDIR /usr/app/baton
