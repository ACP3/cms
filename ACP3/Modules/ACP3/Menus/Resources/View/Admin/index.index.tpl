{extends file="asset:System/layout.admin-grid.tpl"}

{$DELETE_ROUTE={uri args="acp/menus/items/delete"}}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="acp/menus/items/create" class="fas fa-plus text-success"}
    {check_access mode="link" path="acp/menus/index/create" class="fas fa-th-list text-info"}
{/block}
{block ADMIN_GRID_CONTENT}
    {if isset($pages_list)}
        <div class="table-responsive">
            <table class="table table-striped table-hover datagrid">
                <thead>
                <tr>
                    {if $can_delete_item === true}
                        <th class="datagrid-column datagrid-column__mass-action">
                            <input type="checkbox" id="mark-all" value="1" {mark name="entries"}>
                        </th>
                    {/if}
                    <th class="datagrid-column__max-width">{lang t="menus|title"}</th>
                    <th>{lang t="menus|page_type"}</th>
                    {if $can_order_item === true}
                        <th class="text-center">{lang t="system|order"}</th>
                    {/if}
                    <th class="text-right">{lang t="system|id"}</th>
                    {if $can_delete_item === true || $can_edit_item === true}
                        <th class="datagrid-column datagrid-column__actions">{lang t="system|action"}</th>
                    {/if}
                </tr>
                </thead>
                <tbody>
                {foreach $pages_list as $block => $values}
                    <tr>
                        <td class="bg-light" colspan="{$colspan}">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    {$values.title}
                                    <span>({lang t="menus|index_name2"} {$block})</span>
                                </div>
                                {if $can_delete || $can_edit}
                                    <div class="d-flex justify-content-end">
                                        {if $can_edit}
                                            <a href="{uri args="acp/menus/index/edit/id_`$values.menu_id`"}"
                                               class="btn btn-outline-secondary btn-sm"
                                               title="{lang t="menus|admin_index_edit"}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        {/if}
                                        {if $can_delete}
                                            <a href="{uri args="acp/menus/index/delete/entries_`$values.menu_id`"}"
                                               class="ml-1 btn btn-danger btn-sm"
                                               title="{lang t="menus|admin_index_delete"}"
                                               data-ajax-form="true"
                                               data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        {/if}
                                    </div>
                                {/if}
                            </div>
                        </td>
                    </tr>
                    {foreach $values.items as $row}
                        <tr>
                            {if $can_delete_item === true}
                                <td class="datagrid-column datagrid-column__mass-action">
                                    <input type="checkbox" name="entries[]" value="{$row.id}">
                                </td>
                            {/if}
                            <td>{$row.spaces}{$row.title}</td>
                            <td>{$row.mode_formatted}</td>
                            {if $can_order_item === true}
                                <td class="text-center">
                                    {if !$row.last}
                                        <a href="{uri args="acp/menus/items/order/id_`$row.id`/action_down"}"
                                           title="{lang t="system|move_down"}"
                                           data-ajax-form="true"
                                           data-ajax-form-loading-text="{lang t="system|loading_please_wait"}"><i class="fas fa-arrow-down" aria-hidden="true"></i></a>
                                    {/if}
                                    {if !$row.first}
                                        <a href="{uri args="acp/menus/items/order/id_`$row.id`/action_up"}"
                                           title="{lang t="system|move_up"}"
                                           data-ajax-form="true"
                                           data-ajax-form-loading-text="{lang t="system|loading_please_wait"}"><i class="fas fa-arrow-up" aria-hidden="true"></i></a>
                                    {/if}
                                    {if $row.first && $row.last}
                                        <i class="fas fa-ban text-danger" aria-hidden="true" title="{lang t="system|move_impossible"}"></i>
                                    {/if}
                                </td>
                            {/if}
                            <td class="text-right">{$row.id}</td>
                            {if $can_delete_item === true || $can_edit_item === true}
                                <td class="datagrid-column datagrid-column__actions">
                                    <div class="d-flex justify-content-end datagrid-column__action-buttons">
                                        {if $can_edit_item === true}
                                            <a href="{uri args="acp/menus/items/edit/id_`$row.id`"}" class="ml-1 btn btn-sm btn-light" title="{lang t="menus|admin_items_edit"}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        {/if}
                                        {if $can_delete_item === true}
                                            <a href="{uri args="acp/menus/items/delete/entries_`$row.id`"}" class="ml-1 btn btn-sm btn-danger" title="{lang t="menus|admin_items_delete"}">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        {/if}
                                    </div>
                                </td>
                            {/if}
                        </tr>
                    {/foreach}
                {/foreach}
                </tbody>
            </table>
        </div>
        {if isset($pages_list)}
            {if $can_delete === true}
                {include file="asset:System/Partials/mark.tpl"}
            {/if}
        {/if}
    {else}
        {include file="asset:System/Partials/no_results.tpl"}
    {/if}
{/block}
{block ADMIN_GRID_MASS_ACTIONS}
    {if isset($pages_list)}
        {check_access mode="button" path="acp/menus/items/delete" class="fas fa-trash" btn_class="btn-sm btn-danger" lang="system|delete_marked" title={lang t="system|delete_marked"}}
    {/if}
{/block}
