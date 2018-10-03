{extends file="asset:System/layout.admin-grid.tpl"}

{$DELETE_ROUTE={uri args="acp/share/index/delete"}}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="acp/share/index/create" class="glyphicon glyphicon-plus text-success"}
    {check_access mode="link" path="acp/share/index/settings" class="glyphicon glyphicon-cog"}
    {if $grid.show_mass_delete}
        {check_access mode="button" path="acp/share/index/delete" class="glyphicon glyphicon-remove text-danger" lang="system|delete_marked"}
    {/if}
{/block}
{block ADMIN_GRID_CONTENT}
    {include file="asset:System/Partials/datagrid.tpl" dataTable=$grid}
{/block}
