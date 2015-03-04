{extends file="asset:layout.tpl"}

{block CONTENT}
    <form action="{uri args="acp/permissions/index/delete"}" method="post">
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
                    {check_access mode="link" path="acp/permissions/index/create" class="glyphicon glyphicon-plus text-success"}
                    {check_access mode="link" path="acp/permissions/resources" class="glyphicon glyphicon-file text-info"}
                    {if isset($roles)}
                        {check_access mode="button" path="acp/permissions/index/delete" class="glyphicon glyphicon-remove text-danger" lang="system|delete_marked"}
                    {/if}
                </div>
            </div>
        </nav>
        {redirect_message}
        {if isset($roles)}
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    {if $can_delete === true}
                        <th style="width:3%"><input type="checkbox" id="mark-all" value="1" {mark name="entries"}></th>
                    {/if}
                    <th>{lang t="system|name"}</th>
                    {if $can_order === true}
                        <th>{lang t="system|order"}</th>
                    {/if}
                    <th style="width:5%">{lang t="system|id"}</th>
                </tr>
                </thead>
                <tbody>
                {foreach $roles as $row}
                    <tr>
                        {if $can_delete === true}
                            <td><input type="checkbox" name="entries[]" value="{$row.id}"></td>
                        {/if}
                        <td>{$row.spaces}{check_access mode="link" path="acp/permissions/index/edit/id_`$row.id`" title=$row.name}</td>
                        {if $can_order === true}
                            <td>
                                {if !$row.last}
                                    <a href="{uri args="acp/permissions/index/order/id_`$row.id`/action_down"}"
                                       title="{lang t="system|move_down"}"><i class="glyphicon glyphicon-arrow-down" aria-hidden="true"></i></a>
                                {/if}
                                {if !$row.first}
                                    <a href="{uri args="acp/permissions/index/order/id_`$row.id`/action_up"}"
                                       title="{lang t="system|move_up"}"><i class="glyphicon glyphicon-arrow-up" aria-hidden="true"></i></a>
                                {/if}
                                {if $row.first && $row.last}
                                    <i class="glyphicon glyphicon-remove-circle text-danger text-danger" aria-hidden="true" title="{lang t="system|move_impossible"}"></i>
                                {/if}
                            </td>
                        {/if}
                        <td>{$row.id}</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
            {if $can_delete === true}
                {include file="asset:system/mark.tpl"}
            {/if}
        {else}
            <div class="alert alert-warning text-center">
                <strong>{lang t="system|no_entries"}</strong>
            </div>
        {/if}
    </form>
{/block}