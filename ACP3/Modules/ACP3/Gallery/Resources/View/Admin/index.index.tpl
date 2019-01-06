{extends file="asset:System/layout.admin-grid.tpl"}

{$DELETE_ROUTE={uri args="acp/gallery/index/delete"}}

{block HEADER_BAR}
    {check_access mode="link" path="acp/gallery/index/create" class="fas fa-plus" btn_class="btn btn-primary" title={lang t="gallery|admin_index_create"}}
    {check_access mode="link" path="acp/gallery/index/settings" class="fas fa-cog" btn_class="btn btn-secondary"}
{/block}
{block ADMIN_GRID_CONTENT}
    {include file="asset:System/Partials/datagrid.tpl" dataTable=$grid}
{/block}
{block ADMIN_GRID_MASS_ACTIONS}
    {if $grid.show_mass_delete}
        {check_access mode="button" path="acp/gallery/index/delete" class="fas fa-trash" btn_class="btn-sm btn-danger" lang="system|delete_marked" title={lang t="system|delete_marked"}}
    {/if}
{/block}
