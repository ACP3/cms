{extends file="asset:System/layout.admin-grid.tpl"}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="acp/contact/index/settings" class="fas fa-cog"}
{/block}
{block ADMIN_GRID_CONTENT}
    {include file="asset:System/Partials/datagrid.tpl" dataTable=$grid}
{/block}
