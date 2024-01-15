FROM php:8.3-cli as build

ARG DEBIAN_FRONTEND=noninteractive

RUN apt update && apt install -y git curl
# Install PHP Extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
        amqp \
        pdo_mysql \
        intl \
        curl \
        pcntl && \
    rm /usr/local/bin/install-php-extensions

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY . /var/www

WORKDIR /var/www
RUN composer install --no-dev -n

# ----------------------------------------------------------------

FROM php:8.3-fpm

# Install PHP Extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
        amqp \
        pdo_mysql \
        intl \
        curl \
        pcntl && \
    rm /usr/local/bin/install-php-extensions

COPY --from=build /var/www /var/www
