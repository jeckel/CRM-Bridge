FROM php:8.3-apache

ENV DEBIAN_FRONTEND=noninteractive
ARG UID
ARG GID

RUN groupadd -g ${GID} hostGroup && \
    useradd -l -u ${UID} -g ${GID} -m -s /bin/bash hostUser

# Install PHP Extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
        amqp \
        pdo_mysql \
        intl && \
    rm /usr/local/bin/install-php-extensions

RUN a2enmod rewrite

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

ENV APACHE_DOCUMENT_ROOT=/var/www/public/
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/docker-php.conf

WORKDIR /var/www
USER hostUser
