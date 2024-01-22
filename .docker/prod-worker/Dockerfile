FROM php:8.3-cli as build

RUN apt update && apt install -y zip

COPY . /application
WORKDIR /application

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV APP_ENV=prod
RUN composer install --no-dev --ignore-platform-reqs
RUN #composer install --no-dev --ignore-platform-reqs --no-plugins --no-scripts

FROM php:8.3-cli

ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get upgrade -y
RUN apt-get install -y --no-install-recommends \
        sudo \
        supervisor \
        librabbitmq-dev

# Install PHP Extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
        amqp \
        pdo_mysql \
        zip \
        intl && \
    rm /usr/local/bin/install-php-extensions


COPY --from=build /application /var/www
ENV APP_ENV=prod

# Configure supervisord
COPY .docker/prod-worker/supervisord.conf /etc/supervisor/supervisord.conf
COPY .docker/prod-worker/php.ini /usr/local/etc/php/conf.d/99-custom.ini
COPY .docker/prod-worker/conf.d /etc/supervisor/conf.d

WORKDIR /var/www
CMD /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
