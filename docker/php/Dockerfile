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

COPY ./app /app/symfony

WORKDIR /app/symfony