{extends file="asset:System/layout.admin-grid.tpl"}

{$DELETE_ROUTE={uri args="acp/newsletter/index/delete"}}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="acp/newsletter/index/create" class="glyphicon glyphicon-plus text-success"}
    {check_access mode="link" path="acp/newsletter/accounts" class="glyphicon glyphicon-user text-info"}
    {check_access mode="link" path="acp/newsletter/index/settings" class="glyphicon glyphicon-cog"}
    {if $show_mass_delete_button}
        {check_access mode="button" path="acp/newsletter/index/delete" class="glyphicon glyphicon-remove text-danger" lang="system|delete_marked"}
    {/if}
{/block}
{block ADMIN_GRID_CONTENT}
    {include file="asset:System/Partials/datagrid.tpl" dataTable=$grid}
{/block}
