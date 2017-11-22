#!/bin/bash

TRAVIS_PHP_VERSION=$1

if [[ ${TRAVIS_PHP_VERSION} = 7.1* ]]
then
    if [ ! -f release.zip ]
    then
        rm -rf build/logs/ ./vendor
        composer install --no-dev --prefer-dist -o -n --ignore-platform-reqs
        zip -qr release.zip . -x *.git* "auth.json" ".codeclimate.yml" ".coveralls.yml" "editorconfig" ".eslintignore" ".eslintrc" ".gitattributes" ".gitignore" ".travis.yml" "./build/travis/*" "./ACP3/Core/Test/*" "./ACP3/Modules/*/Test/*"
    fi
fi
