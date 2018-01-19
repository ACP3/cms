#!/bin/bash

if [[ -n ${CI_COMMIT_TAG} ]]
then
    if [[ ${CI_COMMIT_TAG} == v* ]]
    then
        CI_COMMIT_TAG_CROPPED=$(echo ${CI_COMMIT_TAG}| cut -d'v' -f 2)
    else
        CI_COMMIT_TAG_CROPPED=${CI_COMMIT_TAG}
    fi

    git clone https://gitlab.com/ACP3/acp3.github.io.git ./build/acp3.github.io
    cd ./build/acp3.github.io
    git checkout master
    rm update.txt
    touch update.txt
    echo "${CI_COMMIT_TAG_CROPPED}||https://github.com/ACP3/cms/releases/tag/${CI_COMMIT_TAG}" >> update.txt
    git add update.txt
    git commit -am "Updated the latest version to ${CI_COMMIT_TAG}"
    git push
fi
