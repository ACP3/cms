<form action="{uri args="acp/permissions/delete_resources"}" method="post">
	<div id="adm-list" class="navbar">
		<div class="navbar-inner navbar-text">
			<h2 class="brand">{lang t="system|overview"}</h2>
			{check_access mode="link" path="acp/permissions/create_resource"  icon="32/resource" lang="permissions|acp_create_resource"}
			{check_access mode="input" path="acp/permissions/delete_resources" icon="32/cancel" lang="system|delete_marked"}
		</div>
	</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($resources)}
{include_js module="permissions" file="acp"}
	<table id="resources-table" class="table table-hover">
		<thead>
			<tr>
{if $can_delete_resource === true}
				<th style="width:3%"><input type="checkbox" id="mark-all" value="1"></th>
{/if}
				<th>{lang t="permissions|filename"}</th>
				<th>{lang t="permissions|assigned_privilege"}</th>
				<th style="width:5%">{lang t="system|id"}</th>
			</tr>
		</thead>
		<tbody>
{foreach $resources as $module => $values}
			<tr>
				<th id="{$values.0.module_id}-resources" class="sub-table-header" colspan="{if $can_delete_resource === true}4{else}3{/if}" style="text-align:left">{$module}</th>
			</tr>
{foreach $values as $row}
			<tr class="hide {$values.0.module_id}-resources">
{if $can_delete_resource === true}
					<td><input type="checkbox" name="entries[]" value="{$row.resource_id}"></td>
{/if}
				<td>{check_access mode="link" path="acp/permissions/edit_resource/id_`$row.resource_id`" title=$row.page}</td>
				<td>{$row.privilege_name}</td>
				<td>{$row.resource_id}</td>
			</tr>
{/foreach}
{/foreach}
		</tbody>
	</table>
{if $can_delete_resource === true}
{mark name="entries"}
{/if}
{else}
	<div class="alert align-center">
		<strong>{lang t="system|no_entries"}</strong>
	</div>
{/if}
</form>