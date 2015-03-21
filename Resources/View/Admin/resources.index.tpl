{extends file="asset:layout.tpl"}

{block CONTENT}
    <form action="{uri args="acp/permissions/resources/delete"}" method="post">
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
                    {check_access mode="link" path="acp/permissions/resources/create"  class="glyphicon glyphicon-plus text-success"}
                    {if isset($resources)}
                        {check_access mode="button" path="acp/permissions/resources/delete" class="glyphicon glyphicon-remove text-danger" lang="system|delete_marked"}
                    {/if}
                </div>
            </div>
        </nav>
        {redirect_message}
        {if isset($resources)}
            <table id="resources-table" class="table table-hover">
                <thead>
                <tr>
                    {if $can_delete_resource === true}
                        <th style="width:3%"><input type="checkbox" id="mark-all" value="1" {mark name="entries"}></th>
                    {/if}
                    <th>{lang t="permissions|area"}</th>
                    <th>{lang t="permissions|controller"}</th>
                    <th>{lang t="permissions|filename"}</th>
                    <th>{lang t="permissions|assigned_privilege"}</th>
                    <th style="width:5%">{lang t="system|id"}</th>
                </tr>
                </thead>
                <tbody>
                {foreach $resources as $module => $values}
                    <tr class="sub-table-header">
                        <th colspan="{if $can_delete_resource === true}6{else}5{/if}">{$module}</th>
                    </tr>
                    {foreach $values as $row}
                        <tr>
                            {if $can_delete_resource === true}
                                <td><input type="checkbox" name="entries[]" value="{$row.resource_id}"></td>
                            {/if}
                            <td>{$row.area}</td>
                            <td>{$row.controller}</td>
                            <td>{check_access mode="link" path="acp/permissions/resources/edit/id_`$row.resource_id`" title=$row.page}</td>
                            <td>{$row.privilege_name}</td>
                            <td>{$row.resource_id}</td>
                        </tr>
                    {/foreach}
                {/foreach}
                </tbody>
            </table>
            {if $can_delete_resource === true}
                {include file="asset:system/mark.tpl"}
            {/if}
            {javascripts}
                {include_js module="permissions" file="admin/resources.index"}
            {/javascripts}
        {else}
            <div class="alert alert-warning text-center">
                <strong>{lang t="system|no_entries"}</strong>
            </div>
        {/if}
    </form>
{/block}