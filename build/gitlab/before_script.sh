#!/bin/sh

eval $(ssh-agent -s)

mkdir -p ~/.ssh
chmod 700 ~/.ssh

echo -e "Host *\n\tStrictHostKeyChecking no\n\n" > ~/.ssh/config

echo "$SSH_PRIVATE_KEY" | tr -d '\r' | ssh-add - > /dev/null
ssh-add -l
