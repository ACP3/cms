{extends file="asset:System/layout.header-bar.tpl"}

{block CONTENT}
    {$smarty.block.parent}
    {redirect_message}
    {block ADMIN_GRID_CONTENT}{/block}
{/block}
