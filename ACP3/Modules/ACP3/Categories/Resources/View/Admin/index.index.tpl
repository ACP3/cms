{extends file="asset:System/layout.admin-grid.tpl"}

{$DELETE_ROUTE={uri args="acp/categories/index/delete"}}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="acp/categories/index/create" class="fas fa-plus text-success"}
    {check_access mode="link" path="acp/categories/index/settings" class="fas fa-cog"}
    {if $grid.show_mass_delete}
        {check_access mode="button" path="acp/categories/index/delete" class="fa fa-trash text-danger" lang="system|delete_marked"}
    {/if}
{/block}
{block ADMIN_GRID_CONTENT}
    {include file="asset:System/Partials/datagrid.tpl" dataTable=$grid}
{/block}
