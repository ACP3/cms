#!/bin/bash

ACP3_CI_USER_GH_TOKEN=$1;
TRAVIS_PHP_VERSION=$2;

if [[ ${TRAVIS_PHP_VERSION} = 7* ]]
then
    phpenv config-rm xdebug.ini
fi

if [[ -n ${ACP3_CI_USER_GH_TOKEN} ]]
then
    composer config github-oauth.github.com ${ACP3_CI_USER_GH_TOKEN}
fi
composer install --prefer-dist --ignore-platform-reqs -n -o
git config --global push.default simple
