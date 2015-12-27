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
            <div class="table-responsive">
                <table id="resources-table" class="table table-hover datagrid">
                    <thead>
                    <tr>
                        {if $can_delete_resource === true}
                            <th class="datagrid-column__mass-action">
                                <input type="checkbox" id="mark-all" value="1" {mark name="entries"}></th>
                        {/if}
                        <th>{lang t="permissions|route"}</th>
                        <th>{lang t="permissions|assigned_privilege"}</th>
                        <th style="width:5%">{lang t="system|id"}</th>
                        {if $can_edit_resource === true || $can_delete_resource === true}
                            <th class="datagrid-column__actions">{lang t="system|action"}</th>
                        {/if}
                    </tr>
                    </thead>
                    <tbody>
                    {foreach $resources as $module => $values}
                        <tr class="sub-table-header">
                            <th colspan="{if $can_delete_resource === true}5{else}4{/if}">{$module}</th>
                        </tr>
                        {foreach $values as $row}
                            <tr>
                                {if $can_delete_resource === true}
                                    <td><input type="checkbox" name="entries[]" value="{$row.resource_id}"></td>
                                {/if}
                                <td>{$row.area}/{$row.controller}/{$row.page}/</td>
                                <td>{$row.privilege_name}</td>
                                <td>{$row.resource_id}</td>
                                {if $can_edit_resource === true || $can_delete_resource === true}
                                    <td>
                                        {if $can_edit_resource === true}
                                            <a href="{uri args="acp/permissions/resources/edit/id_`$row.resource_id`"}"
                                               class="btn btn-default btn-block btn-xs"
                                               title="{lang t="permissions|admin_resources_edit"}">
                                                <i class="glyphicon glyphicon-edit"></i>
                                                {lang t="system|edit"}
                                            </a>
                                        {/if}
                                        {if $can_delete_resource === true}
                                            <a href="{uri args="acp/permissions/resources/delete/entries_`$row.resource_id`"}"
                                               class="btn btn-danger btn-block btn-xs"
                                               title="{lang t="permissions|admin_resources_delete"}">
                                                <i class="glyphicon glyphicon-remove"></i>
                                                {lang t="system|delete"}
                                            </a>
                                        {/if}
                                    </td>
                                {/if}
                            </tr>
                        {/foreach}
                    {/foreach}
                    </tbody>
                </table>
            </div>
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