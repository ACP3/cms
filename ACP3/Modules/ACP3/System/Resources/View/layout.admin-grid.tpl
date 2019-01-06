{extends file="asset:System/layout.header-bar.tpl"}

{block CONTENT}
    {if !empty($DELETE_ROUTE)}
        <form action="{$DELETE_ROUTE}" method="post">
            {$smarty.block.parent}
            {redirect_message}
            {block ADMIN_GRID_CONTENT}{/block}

            <div class="datagrid-mass-actions py-2 shadow-lg bg-dark d-none">
                <div class="container text-right">
                    {block ADMIN_GRID_MASS_ACTIONS}{/block}
                </div>
            </div>
        </form>
    {else}
        {$smarty.block.parent}
        {redirect_message}
        {block ADMIN_GRID_CONTENT}{/block}
    {/if}
{/block}
