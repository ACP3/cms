{extends file="asset:System/layout.admin-grid.tpl"}

{$DELETE_ROUTE={uri args="acp/permissions/index/delete"}}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="acp/permissions/index/create" iconSet="solid" icon="plus" class="text-success"}
    {check_access mode="link" path="acp/permissions/resources" iconSet="solid" icon="file" class="text-info"}
{/block}
{block ADMIN_GRID_CONTENT}
    {include file="asset:System/Partials/datagrid.tpl" dataTable=$grid}
{/block}
