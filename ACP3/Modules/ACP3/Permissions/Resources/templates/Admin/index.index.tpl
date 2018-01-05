{extends file="asset:System/layout.admin-grid.tpl"}

{$DELETE_ROUTE={uri args="acp/permissions/index/delete"}}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="acp/permissions/index/create" class="fa fa-plus text-success"}
    {check_access mode="link" path="acp/permissions/resources" class="fa fa-file text-info"}
    {if $show_mass_delete_button}
        {check_access mode="button" path="acp/permissions/index/delete" class="fa fa-trash text-danger" lang="system|delete_marked"}
    {/if}
{/block}
{block ADMIN_GRID_CONTENT}
    {include file="asset:System/Partials/datagrid.tpl" dataTable=$grid}
{/block}
