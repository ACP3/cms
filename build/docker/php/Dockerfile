ARG PHP_VERSION=""
FROM php:${PHP_VERSION:+${PHP_VERSION}-}fpm

RUN apt-get update -yqq && \
    apt-get install git \
                    zlib1g-dev \
                    unzip \
                    libpng-dev \
                    libfreetype6-dev \
                    libjpeg62-turbo-dev \
                    libicu-dev -yqq && \
    (which ssh-agent || (apt-get install openssh-client -y)) && \
    docker-php-ext-install -j$(nproc) zip \
                                      intl \
                                      pdo_mysql && \
    docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && \
    docker-php-ext-install -j$(nproc) gd && \
    apt-get autoremove -y && \
    apt-get clean -y

RUN usermod -u 1000 www-data

RUN curl --silent --show-error https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    composer global require hirak/prestissimo
