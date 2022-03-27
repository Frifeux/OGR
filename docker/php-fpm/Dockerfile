FROM php:8.1-fpm

RUN apt update \
    && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev libldap2-dev \
    && docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu \
    && docker-php-ext-install intl opcache pdo pdo_mysql ldap\
    && pecl install apcu \
    && docker-php-ext-enable apcu ldap \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip
