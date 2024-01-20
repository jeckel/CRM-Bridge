FROM php:8.1-cli as build

COPY . /application
WORKDIR /application

RUN apt update && apt install -y zip

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN composer install --no-dev --ignore-platform-reqs --no-plugins --no-scripts

FROM php:8.3-apache

ENV DEBIAN_FRONTEND=noninteractive

# Install PHP Extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
        amqp \
        pdo_mysql \
        zip \
        intl && \
    rm /usr/local/bin/install-php-extensions

RUN a2enmod rewrite

COPY .docker/prod-web/000-default.conf /etc/apache2/sites-available/000-default.conf

ENV APACHE_DOCUMENT_ROOT=/var/www/public/
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/docker-php.conf

COPY --from=build /application /var/www

WORKDIR /var/www
