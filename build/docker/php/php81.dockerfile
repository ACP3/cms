FROM php:8.1-apache AS dev

RUN apt-get update -yqq && \
    apt-get install git \
                    unzip \
                    zlib1g-dev \
                    libzip-dev \
                    libpng-dev \
                    libwebp-dev \
                    libfreetype6-dev \
                    libjpeg62-turbo-dev \
                    libicu-dev -yqq && \
    (which ssh-agent || (apt-get install openssh-client -y)) && \
    docker-php-ext-install -j$(nproc) zip \
                                      intl \
                                      pdo_mysql && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install -j$(nproc) gd && \
    a2enmod rewrite && \
    apt-get autoremove -y && \
    apt-get clean -y

RUN usermod -u 1000 www-data

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
 && php composer-setup.php --quiet --2 \
 && mv composer.phar /usr/local/bin/composer \
 && rm composer-setup.php

FROM node:20 AS node
FROM dev as ci

COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node /usr/local/bin/node /usr/local/bin/node

RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm
