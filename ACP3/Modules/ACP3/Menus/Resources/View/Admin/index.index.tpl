{extends file="asset:System/layout.admin-grid.tpl"}

{$DELETE_ROUTE={uri args="acp/menus/items/delete"}}

{block HEADER_BAR_OPTIONS}
    {include file="asset:System/Partials/ajax-modal-edit.tpl" modal=['acl_resource' => 'admin/menus/items/create', 'button_icon' => 'plus', 'button_icon_selectors' => 'text-success', 'button_selectors' => ' ', 'edit_path' => "acp/menus/items/create/", 'title' => {lang t="menus|admin_items_create"}]}
    {include file="asset:System/Partials/ajax-modal-edit.tpl" modal=['acl_resource' => 'admin/menus/index/create', 'button_icon' => 'table-list', 'button_icon_selectors' => 'text-info', 'button_selectors' => ' ', 'edit_path' => "acp/menus/index/create/", 'title' => {lang t="menus|admin_index_create"}]}
{/block}
{block ADMIN_GRID_CONTENT}
    {if !empty($menus)}
        {tabset identifier="menu-item-admin-grid"}
            {foreach $menus as $menu}
                {tab title=$menu.title}
                    <div class="row">
                        <div class="align-self-center col-md-auto mb-3 mb-md-0">
                            <strong>{lang t="menus|index_name2"}</strong> {$menu.index_name}
                        </div>
                        {if $can_edit || $can_delete}
                            <div class="col-md text-start text-md-end">
                                {if $can_edit}
                                    <a href="{uri args="acp/menus/index/edit/id_`$menu.id`"}" class="btn btn-light">
                                        {icon iconSet="solid" icon="pen"} {lang t="menus|admin_index_edit"}
                                    </a>
                                {/if}
                                {if $can_delete}
                                    <a href="{uri args="acp/menus/index/delete/entries_`$menu.id`"}" class="btn btn-danger">
                                        {icon iconSet="solid" icon="trash"} {lang t="menus|admin_index_delete"}
                                    </a>
                                {/if}
                            </div>
                        {/if}
                    </div>
                    <hr>
                    {include file="asset:System/Partials/datagrid.tpl" dataTable=$data_grids[$menu.id]['grid']}
                {/tab}
            {/foreach}
        {/tabset}
    {else}
        {include file="asset:System/Partials/no_results.tpl"}
    {/if}
{/block}
