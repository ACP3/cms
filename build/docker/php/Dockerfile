ARG PHP_VERSION=""
FROM php:${PHP_VERSION:+${PHP_VERSION}-}fpm

RUN apt-get update -yqq && \
    apt-get install git \
                    zlib1g-dev \
                    libzip-dev \
                    libpng-dev \
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
    docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && \
    docker-php-ext-install -j$(nproc) gd && \
    apt-get autoremove -y && \
    apt-get clean -y

RUN usermod -u 1000 www-data

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
 && php composer-setup.php --quiet --1 \
 && mv composer.phar /usr/local/bin/composer \
 && rm composer-setup.php
