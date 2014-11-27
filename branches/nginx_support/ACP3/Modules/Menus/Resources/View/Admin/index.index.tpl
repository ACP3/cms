{extends file="asset:layout.tpl"}

{block CONTENT}
    <form action="{uri args="acp/menus/items/delete"}" method="post">
        <nav id="adm-list" class="navbar navbar-default" role="navigation">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex2-collapse">
                    <span class="sr-only">{lang t="system|toggle_navigation"}</span>
                    <span class="icon-bar"></span> <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <span class="navbar-brand">{lang t="system|overview"}</span>
            </div>
            <div class="collapse navbar-collapse navbar-ex2-collapse">
                <div class="navbar-text pull-right">
                    {check_access mode="link" path="acp/menus/items/create" icon="32/kmenuedit" width="32" height="32"}
                    {check_access mode="link" path="acp/menus/index/create" icon="32/source_moc" width="32" height="32"}
                    {check_access mode="input" path="acp/menus/items/delete" icon="32/cancel" lang="system|delete_marked"}
                </div>
            </div>
        </nav>
        {redirect_message}
        {if isset($pages_list)}
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    {if $can_delete_item === true}
                        <th style="width:3%"><input type="checkbox" id="mark-all" value="1" {mark name="entries"}></th>
                    {/if}
                    <th style="width:30%">{lang t="menus|title"}</th>
                    <th>{lang t="menus|page_type"}</th>
                    {if $can_order_item === true}
                        <th>{lang t="system|order"}</th>
                    {/if}
                    <th style="width:5%">{lang t="system|id"}</th>
                </tr>
                </thead>
                <tbody>
                {foreach $pages_list as $block => $values}
                    <tr>
                        <td class="sub-table-header" colspan="{$colspan}">
                            {$values.title} <span>({lang t="menus|index_name2"} {$block})</span>
                            {if $can_delete || $can_edit}
                                <div class="btn-group pull-right">
                                    {if $can_edit}
                                        <a href="{uri args="acp/menus/index/edit/id_`$values.menu_id`"}" class="btn btn-default btn-sm" title="{lang t="menus|admin_index_edit"}">
                                            <i class="glyphicon glyphicon-edit"></i>
                                            {lang t="system|edit"}
                                        </a>
                                    {/if}
                                    {if $can_delete}
                                        <a href="{uri args="acp/menus/index/delete/entries_`$values.menu_id`"}" class="btn btn-default btn-sm" title="{lang t="menus|admin_index_delete"}" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
                                            <i class="glyphicon glyphicon-remove"></i>
                                            {lang t="system|delete"}
                                        </a>
                                    {/if}
                                </div>
                            {/if}
                        </td>
                    </tr>
                    {foreach $values.items as $row}
                        <tr>
                            {if $can_delete_item === true}
                                <td><input type="checkbox" name="entries[]" value="{$row.id}"></td>
                            {/if}
                            <td>{$row.spaces}{check_access mode="link" path="acp/menus/items/edit/id_`$row.id`" title=$row.title}</td>
                            <td>{$row.mode_formatted}</td>
                            {if $can_order_item === true}
                                <td>
                                    {if !$row.last}
                                        <a href="{uri args="acp/menus/items/order/id_`$row.id`/action_down"}" title="{lang t="system|move_down"}" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
                                            {icon path="16/down" width="16" height="16" alt={lang t="system|move_down"}}
                                        </a>
                                    {/if}
                                    {if !$row.first}
                                        <a href="{uri args="acp/menus/items/order/id_`$row.id`/action_up"}" title="{lang t="system|move_up"}" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
                                            {icon path="16/up" width="16" height="16" alt={lang t="system|move_up"}}
                                        </a>
                                    {/if}
                                    {if $row.first && $row.last}
                                        {icon path="16/editdelete" width="16" height="16" alt={lang t="system|move_impossible"} title={lang t="system|move_impossible"}}
                                    {/if}
                                </td>
                            {/if}
                            <td>{$row.id}</td>
                        </tr>
                    {/foreach}
                {/foreach}
                </tbody>
            </table>
        {else}
            <div class="alert alert-warning text-center">
                <strong>{lang t="system|no_entries"}</strong>
            </div>
        {/if}
    </form>
    {if isset($pages_list)}
        {javascripts}
            {include_js module="system" file="forms"}
        {/javascripts}
        {if $can_delete === true}
            {include file="asset:system/mark.tpl"}
        {/if}
    {/if}
{/block}