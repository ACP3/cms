{extends file="asset:System/layout.email.tpl"}

{block EMAIL_HEAD}
    <title>{$mail.title}</title>
{/block}

{block EMAIL_CONTENT}
    {$mail.body}
    {if !empty($mail.signature)}
        <hr>
        {$mail.signature}
    {/if}
{/block}
