{extends file="asset:System/layout.admin-grid.tpl"}

{block HEADER_BAR}
    {check_access mode="link" path="acp/contact/index/settings" class="fas fa-cog" btn_class="btn btn-secondary" title={lang t="conact|admin_index_settings"}}
{/block}
{block ADMIN_GRID_CONTENT}
    {include file="asset:System/Partials/datagrid.tpl" dataTable=$grid}
{/block}
