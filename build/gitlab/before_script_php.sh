#!/bin/bash

# We need to install dependencies only for Docker
[[ ! -e /.dockerenv ]] && [[ ! -e /.dockerinit ]] && exit 0

# Install apt-utils, git and unzip, the php image doesn't have installed
apt-get update -yqq
apt-get install zlib1g-dev unzip libicu-dev -yqq

# Install required PHP extensions for the tests etc.
docker-php-ext-install zip

# Install composer
curl --silent --show-error https://getcomposer.org/installer | php
