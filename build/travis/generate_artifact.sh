#!/bin/bash

TRAVIS_PHP_VERSION=$1

if [[ ${TRAVIS_PHP_VERSION} = "7.0" ]]
then
    rm -rf build/logs/ ./vendor
    composer install --no-dev --prefer-dist -o -n --ignore-platform-reqs
    zip -qr release.zip . -x *.git* "auth.json" ".codeclimate.yml" ".coveralls.yml" ".gitattributes" ".travis.yml" "./build/travis/*"
fi
