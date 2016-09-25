#!/bin/bash

TRAVIS_TAG=$1

composer create-project acp3/subtree-pushes -s dev --prefer-dist -n ./build/subtree-pushes
./build/subtree-pushes/exec-command.sh ${TRAVIS_TAG};
