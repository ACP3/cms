{extends file="asset:System/layout.header-bar.tpl"}

{block CONTENT}
    {if !empty($DELETE_ROUTE)}
        <form action="{$DELETE_ROUTE}" method="post">
            {$smarty.block.parent}
            {redirect_message}
            {block ADMIN_GRID_CONTENT}{/block}
        </form>
    {else}
        {$smarty.block.parent}
        {redirect_message}
        {block ADMIN_GRID_CONTENT}{/block}
    {/if}
{/block}
