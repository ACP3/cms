#!/bin/bash

TRAVIS_PHP_VERSION=$1

if [[ ${TRAVIS_PHP_VERSION} != "hhvm" && ${TRAVIS_PHP_VERSION} != "nightly" ]]
then
    phpdbg ./vendor/bin/phpunit -c ./tests/phpunit.dist.xml --coverage-clover ./build/logs/clover.xml
else
    php ./vendor/bin/phpunit -c ./tests/phpunit.dist.xml
fi
