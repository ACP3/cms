#!/bin/bash

TRAVIS_PHP_VERSION=$1

if [[ ${TRAVIS_PHP_VERSION} != "nightly" ]]
then
    travis_retry vendor/bin/coveralls -v
fi
