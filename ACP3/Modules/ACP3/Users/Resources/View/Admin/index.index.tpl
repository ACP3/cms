{extends file="asset:System/layout.admin-grid.tpl"}

{$DELETE_ROUTE={uri args="acp/users/index/delete"}}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="acp/users/index/create" class="fas fa-plus text-success"}
    {check_access mode="link" path="acp/users/index/settings" class="fas fa-cog"}
    {if $grid.show_mass_delete}
        {check_access mode="button" path="acp/users/index/delete" class="fas fa-trash text-danger" lang="system|delete_marked"}
    {/if}
{/block}
{block ADMIN_GRID_CONTENT}
    {include file="asset:System/Partials/datagrid.tpl" dataTable=$grid}
{/block}
