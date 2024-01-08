FROM php:8.3-rc-fpm

ENV DEBIAN_FRONTEND=noninteractive
ARG UID
ARG GID

RUN groupadd -g ${GID} hostGroup && \
    useradd -l -u ${UID} -g ${GID} -m -s /bin/bash hostUser

# Install PHP Extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
        pdo_mysql \
        intl && \
    rm /usr/local/bin/install-php-extensions

# Configure php-fpm
COPY zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
RUN mkdir -p /var/run/php/ && chmod -R +X /var/run/php && chown hostUser:hostGroup /var/run/php
