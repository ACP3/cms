{extends file="asset:System/layout.admin-grid.tpl"}

{$DELETE_ROUTE={uri args="acp/articles/index/delete"}}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="acp/articles/index/create" class="fa fa-plus text-success"}
    {if $show_mass_delete_button}
        {check_access mode="button" path="acp/articles/index/delete" class="fa fa-trash text-danger" lang="system|delete_marked"}
    {/if}
{/block}
{block ADMIN_GRID_CONTENT}
    {include file="asset:System/Partials/datagrid.tpl" dataTable=$grid}
{/block}
