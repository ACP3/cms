#!/bin/bash

TRAVIS_PHP_VERSION=$1

if [ ${TRAVIS_PHP_VERSION} != "hhvm" && ${TRAVIS_PHP_VERSION} != "7.1" && ${TRAVIS_PHP_VERSION} != "nightly" ]
then
    travis_retry php vendor/bin/coveralls -v
fi
