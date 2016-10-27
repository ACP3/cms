#!/bin/bash

TRAVIS_TAG=$2

if [[ -n ${TRAVIS_TAG} ]]
then
    if [[ ${TRAVIS_TAG} == v* ]]
    then
        TRAVIS_TAG_CROPPED=$(echo ${TRAVIS_TAG}| cut -d'v' -f 2)
    else
        TRAVIS_TAG_CROPPED=${TRAVIS_TAG}
    fi

    git clone https://github.com/ACP3/acp3.github.io.git ./build/acp3.github.io
    cd ./build/acp3.github.io
    git checkout master
    rm update.txt
    touch update.txt
    echo "${TRAVIS_TAG_CROPPED}||https://github.com/ACP3/cms/releases/tag/${TRAVIS_TAG}" >> update.txt
    git add update.txt
    git commit -am "Updated the latest version to ${TRAVIS_TAG}"
    git push
fi
