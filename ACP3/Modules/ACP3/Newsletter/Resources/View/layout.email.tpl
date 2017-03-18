{extends file="asset:System/layout.email.tpl"}

{block EMAIL_HEAD}
    <title>{$mail.title}</title>
{/block}

{block EMAIL_CONTENT_BEFORE}
    <p style="text-align:center">
        <a href="{$mail.url_web_view}">{lang t="newsletter|use_web_view"}</a>
    </p>
{/block}

{block EMAIL_CONTENT}
    <h1>{$mail.title}</h1>
    {$mail.body}
    <hr>
    {$mail.signature}
{/block}
