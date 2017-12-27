#!/usr/bin/env bash

which curl || (apt-get install curl -y)

declare -a repositories

repositories[0]="https://github.com/ACP3/core.git"
repositories[1]="https://github.com/ACP3/module-acp.git"
repositories[2]="https://github.com/ACP3/module-articles.git"
repositories[3]="https://github.com/ACP3/module-captcha.git"
repositories[4]="https://github.com/ACP3/module-categories.git"
repositories[5]="https://github.com/ACP3/module-comments.git"
repositories[6]="https://github.com/ACP3/module-contact.git"
repositories[7]="https://github.com/ACP3/module-emoticons.git"
repositories[8]="https://github.com/ACP3/module-errors.git"
repositories[9]="https://github.com/ACP3/module-feeds.git"
repositories[10]="https://github.com/ACP3/module-filemanager.git"
repositories[11]="https://github.com/ACP3/module-files.git"
repositories[12]="https://github.com/ACP3/module-gallery.git"
repositories[13]="https://github.com/ACP3/module-guestbook.git"
repositories[14]="https://github.com/ACP3/module-menus.git"
repositories[15]="https://github.com/ACP3/module-news.git"
repositories[16]="https://github.com/ACP3/module-newsletter.git"
repositories[17]="https://github.com/ACP3/module-permissions.git"
repositories[18]="https://github.com/ACP3/module-polls.git"
repositories[19]="https://github.com/ACP3/module-search.git"
repositories[20]="https://github.com/ACP3/module-seo.git"
repositories[21]="https://github.com/ACP3/module-system.git"
repositories[22]="https://github.com/ACP3/module-users.git"
repositories[23]="https://github.com/ACP3/module-wysiwyg-ckeditor.git"
repositories[24]="https://github.com/ACP3/module-wysiwyg-tinymce.git"
repositories[25]="https://github.com/ACP3/setup.git"
repositories[26]="https://github.com/ACP3/test.git"
repositories[27]="https://github.com/ACP3/theme-default.git"

for i in "${!repositories[@]}"
do
    echo ${repositories[$i]};
    echo "\n";
    curl -XPOST -H'content-type:application/json' "https://packagist.org/api/update-package?username=${PACKAGIST_USER_NAME}&apiToken=${PACKAGIST_API_TOKEN}" -d"{\"repository\":{\"url\":\"${repositories[$i]}\"}}"
    echo "\n\n";
done
