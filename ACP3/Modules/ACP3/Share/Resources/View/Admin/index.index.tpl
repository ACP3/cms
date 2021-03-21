{extends file="asset:System/layout.admin-grid.tpl"}

{$DELETE_ROUTE={uri args="acp/share/index/delete"}}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="acp/share/index/create" iconSet="solid" icon="plus" class="text-success"}
    {check_access mode="link" path="acp/share/index/settings" iconSet="solid" icon="cog"}
{/block}
{block ADMIN_GRID_CONTENT}
    {include file="asset:System/Partials/datagrid.tpl" dataTable=$grid}
{/block}
