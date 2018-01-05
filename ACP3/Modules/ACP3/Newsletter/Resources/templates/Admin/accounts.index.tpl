{extends file="asset:System/layout.admin-grid.tpl"}

{$DELETE_ROUTE={uri args="acp/newsletter/accounts/delete"}}

{block HEADER_BAR_OPTIONS}
    {check_access mode="button" path="acp/newsletter/accounts/delete" class="fa fa-trash text-danger" lang="system|delete_marked"}
{/block}
{block ADMIN_GRID_CONTENT}
    {include file="asset:System/Partials/datagrid.tpl" dataTable=$grid}
{/block}
