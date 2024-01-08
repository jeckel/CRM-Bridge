FROM php:8.3-cli

ARG DEBIAN_FRONTEND=noninteractive
ARG UID
ARG GID
RUN groupadd -g ${GID} hostGroup && \
    useradd -l -u ${UID} -g ${GID} -m -s /bin/bash hostUser

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
        intl && \
    rm /usr/local/bin/install-php-extensions

#RUN pecl config-set php_ini /etc/php.ini &&  \
#    pecl install amqp

# Configure supervisord
COPY supervisord.conf /etc/supervisor/supervisord.conf
COPY php.ini /usr/local/etc/php/conf.d/99-custom.ini

# Launch supervisor
EXPOSE 9001
LABEL "traefik.enable"=true
WORKDIR /var/www/project
CMD /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
