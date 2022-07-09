FROM php:8-cli-alpine as base

WORKDIR /app

RUN apk update; \
    apk add --no-cache libzip-dev icu-dev curl-dev tzdata git ssh wget; \
    docker-php-ext-install pdo_mysql zip intl curl; \
    apk del gcc g++;

FROM base as builder

COPY . .
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN composer install
