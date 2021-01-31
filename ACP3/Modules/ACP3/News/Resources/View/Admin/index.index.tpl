{extends file="asset:System/layout.admin-grid.tpl"}

{$DELETE_ROUTE={uri args="acp/news/index/delete"}}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="acp/news/index/create" class="fas fa-plus text-success"}
    {check_access mode="link" path="acp/news/index/settings" class="fas fa-cog"}
{/block}
{block ADMIN_GRID_CONTENT}
    {include file="asset:System/Partials/datagrid.tpl" dataTable=$grid}
{/block}
