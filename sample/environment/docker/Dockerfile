FROM php:7.1-fpm-alpine

# Install Postgres dev first to prevent pdo_pgsql error
RUN set -ex \
  && apk --no-cache add \
    postgresql-dev

# Install PDO extensions
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql

# Install XDebug
ARG INSTALL_XDEBUG
RUN if [ "${INSTALL_XDEBUG}" == true ]; then \
        apk update && apk add g++ autoconf make \
        && pecl install xdebug \
        && docker-php-ext-enable xdebug \
        && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
        && echo "xdebug.remote_handler=dbgp" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
        && echo "xdebug.remote_mode=req" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
        && echo "xdebug.remote_host=docker.local" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
        && echo "xdebug.remote_port=9000" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
        && apk del --purge g++ ;\
    fi