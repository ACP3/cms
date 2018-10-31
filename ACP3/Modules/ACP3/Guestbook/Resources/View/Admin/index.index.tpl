{extends file="asset:System/layout.admin-grid.tpl"}

{$DELETE_ROUTE={uri args="acp/guestbook/index/delete"}}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="acp/guestbook/index/settings" class="fas fa-cog"}
    {if $grid.show_mass_delete}
        {check_access mode="button" path="acp/guestbook/index/delete" class="fas fa-trash text-danger" lang="system|delete_marked"}
    {/if}
{/block}
{block ADMIN_GRID_CONTENT}
    {include file="asset:System/Partials/datagrid.tpl" dataTable=$grid}
{/block}
