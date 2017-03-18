{extends file="asset:System/layout.email.tpl"}

{block EMAIL_CONTENT}
    <h1>{$mail.title}</h1>
    {$smarty.block.parent}
{/block}
