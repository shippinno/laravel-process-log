FROM php:8.2-fpm-alpine

RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer

RUN apk update \
    && apk add  --no-cache g++ make autoconf \
    && apk add git

RUN apk add --update linux-headers
RUN pecl install xdebug-3.2.2
RUN docker-php-ext-enable xdebug

RUN mkdir /code
WORKDIR /code