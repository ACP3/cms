{extends file="asset:System/layout.admin-grid.tpl"}

{$DELETE_ROUTE={uri args="acp/seo/index/delete"}}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="acp/seo/index/create" iconSet="solid" icon="plus" class="text-success"}
    {check_access mode="link" path="acp/seo/index/settings" iconSet="solid" icon="gear"}
{/block}
{block ADMIN_GRID_CONTENT}
    {include file="asset:System/Partials/datagrid.tpl" dataTable=$grid}
{/block}
