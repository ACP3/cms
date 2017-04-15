#!/bin/bash

BRANCH_NAME='sami-update'

curl -O http://get.sensiolabs.org/sami.phar

git clone https://github.com/ACP3/acp3-api.github.io.git ./build/acp3-api.github.io
cd ./build/acp3-api.github.io
git checkout -q -b ${BRANCH_NAME}
git rm -q -r .
php ../../sami.phar update ../../.sami.php
cp -r ../sami/docs/* .
git add .
git commit -am "Updated the API docs"
git checkout -q master
git merge -q ${BRANCH_NAME}
git branch -q -d ${BRANCH_NAME}
git push --all
