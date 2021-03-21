{extends file="asset:System/layout.admin-grid.tpl"}

{$DELETE_ROUTE={uri args="acp/contact/index/delete"}}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="acp/contact/index/settings" iconSet="solid" icon="cog"}
{/block}
{block ADMIN_GRID_CONTENT}
    {include file="asset:System/Partials/datagrid.tpl" dataTable=$grid}
{/block}
