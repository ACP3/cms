{extends file="asset:System/layout.admin-grid.tpl"}

{$DELETE_ROUTE={uri args="acp/menus/items/delete"}}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="acp/menus/items/create" class="fas fa-plus text-success"}
    {check_access mode="link" path="acp/menus/index/create" class="fas fa-th-list text-info"}
    {foreach $data_grids as $data_grid}
        {if $data_grid.grid.show_mass_delete}
            {check_access mode="button" path="acp/menus/items/delete" class="fa fa-trash text-danger" lang="system|delete_marked"}
            {break}
        {/if}
    {/foreach}
{/block}
{block ADMIN_GRID_CONTENT}
    {if !empty($menus)}
        <ul class="nav nav-tabs" role="tablist">
            {foreach $menus $menu}
                <li role="presentation"{if $menu@first} class="active"{/if}>
                    <a href="#tab-{$menu.id}" aria-controls="tab-{$menu.id}" role="tab" data-toggle="tab">
                        {$menu.title}
                    </a>
                </li>
            {/foreach}
        </ul>
        <div class="tab-content">
            {foreach $menus $menu}
                <div class="tab-pane fade{if $menu@first} in active{/if}" role="tabpanel" id="tab-{$menu.id}">
                    <div class="row">
                        <div class="{if $can_edit || $can_delete}col-sm-6{else}col-sm-12{/if}">
                            <div class="form-control-static"><strong>{lang t="menus|index_name2"}</strong> {$menu.index_name}</div>
                        </div>
                        {if $can_edit || $can_delete}
                            <div class="col-sm-6 text-right">
                                {if $can_edit}
                                    <a href="{uri args="acp/menus/index/edit/id_`$menu.id`"}" class="btn btn-default">
                                        <i class="fa fa-edit"></i> {lang t="menus|admin_index_edit"}
                                    </a>
                                {/if}
                                {if $can_delete}
                                    <a href="{uri args="acp/menus/index/delete/entries_`$menu.id`"}" class="btn btn-danger">
                                        <i class="fa fa-trash"></i> {lang t="menus|admin_index_delete"}
                                    </a>
                                {/if}
                            </div>
                        {/if}
                    </div>
                    <hr>
                    {include file="asset:System/Partials/datagrid.tpl" dataTable=$data_grids[$menu.id]['grid']}
                </div>
            {/foreach}
        </div>
    {else}
        {include file="asset:System/Partials/no_results.tpl"}
    {/if}
{/block}
