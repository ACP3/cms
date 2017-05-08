#!/bin/bash

BRANCH_NAME='sami-update'
REPO_DIR='apidocs'

curl -O http://get.sensiolabs.org/sami.phar
php sami.phar update .sami.php

git clone https://github.com/ACP3/${REPO_DIR}.git ./build/${REPO_DIR}
cd ./build/${REPO_DIR}
git checkout -q -b ${BRANCH_NAME}
git rm -q -r .
cp -a ../sami/docs/* .
git add .
git commit -am "Updated the API docs"
git checkout -q master
git merge -q ${BRANCH_NAME}
git branch -q -d ${BRANCH_NAME}
git push --all
