FROM composer

FROM php:7.2-fpm

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

ENV APCU_VERSION 5.1.9

RUN apt-get update && apt-get install -qq \
        libxml2-dev \
        libldb-dev \
        libldap2-dev \
        librabbitmq-dev \
        zlib1g-dev \
        git \
        bash \
        unzip

RUN apt-get autoclean

RUN docker-php-ext-install \
        intl \
        ldap \
        pcntl \
        soap \
        sockets \
        zip

RUN pecl install apcu-${APCU_VERSION} xdebug amqp
RUN docker-php-ext-enable \
        amqp \
        apcu \
        opcache \
        xdebug

COPY php.ini /usr/local/etc/php/php.ini
