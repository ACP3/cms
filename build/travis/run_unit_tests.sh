#!/bin/bash

TRAVIS_PHP_VERSION=$1

if [[ ${TRAVIS_PHP_VERSION} = "5.6" || ${TRAVIS_PHP_VERSION} = "hhvm" ]]
then
    php ./vendor/bin/phpunit -c ./tests/phpunit.dist.xml --coverage-clover ./build/logs/clover.xml
elif [[ ${TRAVIS_PHP_VERSION} != "nightly" ]]
then
    phpdbg -qrr ./vendor/bin/phpunit -c ./tests/phpunit.dist.xml --coverage-clover ./build/logs/clover.xml
else
    php ./vendor/bin/phpunit -c ./tests/phpunit.dist.xml
fi
