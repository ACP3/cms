#!/bin/bash

# We need to install dependencies only for Docker
[[ ! -e /.dockerenv ]] && [[ ! -e /.dockerinit ]] && exit 0

# Install apt-utils, git and unzip, the php image doesn't have installed
apt-get update -yqq
apt-get install git zlib1g-dev unzip libicu-dev -yqq

pecl install xdebug

# Install required PHP extensions for the tests etc.
docker-php-ext-install zip
docker-php-ext-enable xdebug

# Install composer
curl --silent --show-error https://getcomposer.org/installer | php
php composer.phar global require hirak/prestissimo
