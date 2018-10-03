{extends file="asset:System/layout.admin-grid.tpl"}

{$DELETE_ROUTE={uri args="acp/gallery/pictures/delete/id_$gallery_id"}}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="acp/gallery/pictures/create/id_`$gallery_id`" class="glyphicon glyphicon-plus text-success"}
    {if $grid.show_mass_delete}
        {check_access mode="button" path="acp/gallery/pictures/delete" class="glyphicon glyphicon-remove text-danger" lang="system|delete_marked"}
    {/if}
{/block}
{block ADMIN_GRID_CONTENT}
    {include file="asset:System/Partials/datagrid.tpl" dataTable=$grid}
{/block}
