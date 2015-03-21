{if $IS_AJAX === true}
    {include file="asset:Guestbook/Frontend/index.create_ajax.tpl"}
{else}
    {include file="asset:Guestbook/Frontend/index.create_normal.tpl"}
{/if}