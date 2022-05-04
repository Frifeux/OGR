FROM php:8.1-fpm

#RUN apt update \
#    && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev libldap2-dev \
#    && docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu \
#    && docker-php-ext-install intl opcache pdo pdo_mysql ldap \
#    && pecl install apcu \
#    && docker-php-ext-enable apcu ldap\
#    && docker-php-ext-configure zip \
#    && docker-php-ext-install zip

#https://github.com/mlocati/docker-php-extension-installer

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions http intl opcache pdo pdo_mysql ldap apcu zip xdebug