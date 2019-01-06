{extends file="asset:System/layout.admin-grid.tpl"}

{$DELETE_ROUTE={uri args="acp/guestbook/index/delete"}}

{block HEADER_BAR}
    {check_access mode="link" path="acp/guestbook/index/settings" class="fas fa-cog" btn_class="btn btn-secondary" title={lang t="guestbook|admin_index_settings"}}
{/block}
{block ADMIN_GRID_CONTENT}
    {include file="asset:System/Partials/datagrid.tpl" dataTable=$grid}
{/block}
{block ADMIN_GRID_MASS_ACTIONS}
    {if $grid.show_mass_delete}
        {check_access mode="button" path="acp/guestbook/index/delete" class="fas fa-trash" btn_class="btn-sm btn-danger" lang="system|delete_marked" title={lang t="system|delete_marked"}}
    {/if}
{/block}
