#!/bin/bash

TRAVIS_PHP_VERSION=$1

if [[ ${TRAVIS_PHP_VERSION} = "7.0" ]]
then
    rm -rf build/logs/* ./vendor
    rm -rf ./vendor
    composer install --no-dev --prefer-dist -o -n --ignore-platform-reqs
    zip -r release.zip ./*
fi
