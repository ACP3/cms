#!/bin/bash

if [[ -n ${CI_COMMIT_TAG} ]]
then
    if [[ ${CI_COMMIT_TAG} == v* ]]
    then
        CI_COMMIT_TAG_CROPPED=$(echo ${CI_COMMIT_TAG}| cut -d'v' -f 2)
    else
        CI_COMMIT_TAG_CROPPED=${CI_COMMIT_TAG}
    fi

    git clone git@gitlab.com:ACP3/update-check.git ./build/update-check
    cd ./build/update-check
    git checkout master
    rm update.txt
    touch update.txt
    echo "${CI_COMMIT_TAG_CROPPED}||https://gitlab.com/ACP3/cms/-/jobs/artifacts/${TRAVIS_TAG}/download?job=deploy%3Agenerate-artifact" >> update.txt
    git add update.txt
    git commit -am "Updated the latest version to ${CI_COMMIT_TAG}"
    git push
fi
