{extends file="asset:System/layout.admin-grid.tpl"}

{$DELETE_ROUTE={uri args="acp/permissions/resources/delete"}}

{block HEADER_BAR_OPTIONS}
    {check_access mode="link" path="acp/permissions/resources/create"  iconSet="solid" icon="plus" class="text-success"}
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
                    <th style="width:5%">{lang t="system|id"}</th>
                    {if $can_edit_resource === true || $can_delete_resource === true}
                        <th class="datagrid-column__actions">{lang t="system|action"}</th>
                    {/if}
                </tr>
                </thead>
                {if $can_delete_resource === true}
                    {include file="asset:System/Partials/datagrid-mass-action-bar.tpl" dataGridIdentifier='resources-table' dataGridColumnCount=5}
                {/if}
                <tbody>
                {foreach $resources as $module => $values}
                    <tr class="table-secondary sub-table-header" data-module-name="{$values[0].module_name}">
                        <th colspan="{if $can_delete_resource === true}5{else}4{/if}">{$module}</th>
                    </tr>
                    {foreach $values as $row}
                        <tr data-module-name="{$row.module_name}">
                            {if $can_delete_resource === true}
                                <td><input type="checkbox" name="entries[]" value="{$row.resource_id}"></td>
                            {/if}
                            <td>{$row.area}/{$row.controller}/{$row.page}/</td>
                            <td>{$row.resource_id}</td>
                            {if $can_edit_resource === true || $can_delete_resource === true}
                                <td class="datagrid-column__actions">
                                    {if $can_edit_resource === true}
                                        <a href="{uri args="acp/permissions/resources/edit/id_`$row.resource_id`"}"
                                           class="btn btn-outline-secondary btn-sm"
                                           title="{lang t="permissions|admin_resources_edit"}">
                                            {icon iconSet="solid" icon="edit"}
                                        </a>
                                    {/if}
                                    {if $can_delete_resource === true}
                                        <a href="{uri args="acp/permissions/resources/delete/entries_`$row.resource_id`"}"
                                           class="btn btn-danger btn-sm"
                                           title="{lang t="permissions|admin_resources_delete"}">
                                            {icon iconSet="solid" icon="trash"}
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
            {include file="asset:System/Partials/mark.tpl" dataGridIdentifier="resources-table"}
        {/if}
        {javascripts}
            {include_js module="permissions" file="admin/resources.index"}
        {/javascripts}
    {else}
        {include file="asset:System/Partials/no_results.tpl"}
    {/if}
{/block}
