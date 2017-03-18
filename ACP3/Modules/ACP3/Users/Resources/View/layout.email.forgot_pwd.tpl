{extends file="asset:System/layout.email.tpl"}

{block EMAIL_HEAD}
    <title>{$mail.title}</title>
{/block}

{block EMAIL_CONTENT}
    {$mail.body}
{/block}
