#!/bin/bash

# We need to install dependencies only for Docker
[[ ! -e /.dockerenv ]] && [[ ! -e /.dockerinit ]] && exit 0

which ssh-agent || (apt-get install openssh-client -y )

eval $(ssh-agent -s)

mkdir -p ~/.ssh
[[ -f /.dockerenv ]] && echo -e "Host *\n\tStrictHostKeyChecking no\n\n" > ~/.ssh/config

ssh-add <(echo "$SSH_PRIVATE_KEY")
ssh-add -l
