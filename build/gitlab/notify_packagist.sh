#!/usr/bin/env bash

which curl || (apt-get install curl -y)

declare -a repositories

repositories+=('https://gitlab.com/ACP3/core.git')
repositories+=('https://gitlab.com/ACP3/module-acp.git')
repositories+=('https://gitlab.com/ACP3/module-articles.git')
repositories+=('https://gitlab.com/ACP3/module-audit-log.git')
repositories+=('https://gitlab.com/ACP3/module-captcha.git')
repositories+=('https://gitlab.com/ACP3/module-categories.git')
repositories+=('https://gitlab.com/ACP3/module-comments.git')
repositories+=('https://gitlab.com/ACP3/module-contact.git')
repositories+=('https://gitlab.com/ACP3/module-emoticons.git')
repositories+=('https://gitlab.com/ACP3/module-errors.git')
repositories+=('https://gitlab.com/ACP3/module-feeds.git')
repositories+=('https://gitlab.com/ACP3/module-filemanager.git')
repositories+=('https://gitlab.com/ACP3/module-files.git')
repositories+=('https://gitlab.com/ACP3/module-gallery.git')
repositories+=('https://gitlab.com/ACP3/module-guestbook.git')
repositories+=('https://gitlab.com/ACP3/module-menus.git')
repositories+=('https://gitlab.com/ACP3/module-news.git')
repositories+=('https://gitlab.com/ACP3/module-newsletter.git')
repositories+=('https://gitlab.com/ACP3/module-permissions.git')
repositories+=('https://gitlab.com/ACP3/module-polls.git')
repositories+=('https://gitlab.com/ACP3/module-search.git')
repositories+=('https://gitlab.com/ACP3/module-seo.git')
repositories+=('https://gitlab.com/ACP3/module-social-sharing.git')
repositories+=('https://gitlab.com/ACP3/module-system.git')
repositories+=('https://gitlab.com/ACP3/module-users.git')
repositories+=('https://gitlab.com/ACP3/module-wysiwyg-ckeditor.git')
repositories+=('https://gitlab.com/ACP3/module-wysiwyg-tinymce.git')
repositories+=('https://gitlab.com/ACP3/setup.git')
repositories+=('https://gitlab.com/ACP3/test.git')
repositories+=('https://gitlab.com/ACP3/theme-default.git')

for i in "${!repositories[@]}"
do
    echo ${repositories[$i]}
    curl -XPOST -H'content-type:application/json' "https://packagist.org/api/update-package?username=${PACKAGIST_USER_NAME}&apiToken=${PACKAGIST_API_TOKEN}" -d"{\"repository\":{\"url\":\"${repositories[$i]}\"}}"
done
