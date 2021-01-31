{extends file="asset:System/layout.admin-grid.tpl"}

{$DELETE_ROUTE={uri args="acp/polls/index/delete"}}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="acp/polls/index/create" class="fas fa-plus text-success"}
{/block}
{block ADMIN_GRID_CONTENT}
    {include file="asset:System/Partials/datagrid.tpl" dataTable=$grid}
{/block}
