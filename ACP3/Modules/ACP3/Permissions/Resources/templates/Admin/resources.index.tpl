{extends file="asset:System/layout.admin-grid.tpl"}

{$DELETE_ROUTE={uri args="acp/permissions/resources/delete"}}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="acp/permissions/resources/manage" class="fa fa-plus text-success" lang="permissions|admin_resources_create"}
    {if isset($resources)}
        {check_access mode="button" path="acp/permissions/resources/delete" class="fa fa-trash text-danger" lang="system|delete_marked"}
    {/if}
{/block}
{block ADMIN_GRID_CONTENT}
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
                                <td class="datagrid-column__actions">
                                    <div class="btn-group pull-right">
                                        {if $can_edit_resource === true}
                                            <a href="{uri args="acp/permissions/resources/manage/id_`$row.resource_id`"}"
                                               class="btn btn-default btn-xs"
                                               title="{lang t="permissions|admin_resources_edit"}">
                                                <i class="fa fa-pencil" aria-hidden="true"></i>
                                            </a>
                                        {/if}
                                        {if $can_delete_resource === true}
                                            <a href="{uri args="acp/permissions/resources/delete/entries_`$row.resource_id`"}"
                                               class="btn btn-danger btn-xs"
                                               title="{lang t="permissions|admin_resources_delete"}">
                                                <i class="fa fa-trash" aria-hidden="true"></i>
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
        {if $can_delete_resource === true}
            {include file="asset:System/Partials/mark.tpl"}
        {/if}
        {javascripts}
        {include_js module="permissions" file="admin/resources.index"}
        {/javascripts}
    {else}
        {include file="asset:System/Partials/no_results.tpl"}
    {/if}
{/block}
