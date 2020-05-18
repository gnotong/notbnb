# ---- Node ----
FROM node:10-alpine AS front
WORKDIR /app/symfony
COPY /app/package*.json ./
COPY /app/yarn.lock ./
COPY /app ./
RUN yarn install && yarn build

# ---- PHP code ----
FROM php:7.4-fpm
RUN docker-php-ext-install pdo_mysql
RUN pecl install apcu
RUN apt-get update && \
    apt-get install -y \
    zlib1g-dev \
    libxml2-dev \
    libzip-dev \
    g++

RUN docker-php-ext-configure intl && \
    docker-php-ext-install zip intl && \
    docker-php-ext-enable apcu

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY ./app /app/symfony

WORKDIR /app/symfony

COPY --from=front /app/symfony/node_modules ./node_modules
COPY --from=front /app/symfony/public/build ./public/build
RUN composer install