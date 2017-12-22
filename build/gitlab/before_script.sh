#!/bin/bash

# We need to install dependencies only for Docker
[[ ! -e /.dockerenv ]] && [[ ! -e /.dockerinit ]] && exit 0

# Install apt-utils, git and unzip, the php image doesn't have installed
apt-get update -yqq
apt-get install git zlib1g-dev unzip libicu-dev -yqq

which ssh-agent || (apt-get install openssh-client -y )

eval $(ssh-agent -s)

mkdir -p ~/.ssh
[[ -f /.dockerenv ]] && echo -e "Host *\n\tStrictHostKeyChecking no\n\n" > ~/.ssh/config

ssh-add <(echo "$SSH_PRIVATE_KEY")
ssh-add -l

# Install required PHP extensions for the tests etc.
docker-php-ext-install zip
