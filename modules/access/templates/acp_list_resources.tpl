<form action="{uri args="acp/access/delete_resources"}" method="post">
	<div id="adm-list">
		{check_access mode="link" uri="acp/access/create_resource"  icon="32/resource" lang="access|create_resource"}
		{check_access mode="input" action="access|acp_delete_resources" icon="32/cancel" lang="common|delete_marked"}
		<h2>{lang t="common|overview"}</h2>
	</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($resources)}
	<script type="text/javascript" src="{$DESIGN_PATH}js/access_admin.js"></script>
	<table id="resources-table" class="acp-table">
		<thead>
			<tr>
{if $can_delete_resource === true}
				<th><input type="checkbox" id="mark-all" value="1" class="checkbox"></th>
{/if}
				<th>{lang t="access|filename"}</th>
				<th>{lang t="access|assigned_privilege"}</th>
				<th style="width:3%">{lang t="common|id"}</th>
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
					<td><input type="checkbox" name="entries[]" value="{$row.resource_id}" class="checkbox"></td>
{/if}
				<td>{check_access mode="link" uri="acp/access/edit_resource/id_`$row.resource_id`" title=$row.page}</td>
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
	<div class="error-box">
		<h5>{lang t="common|no_entries"}</h5>
	</div>
{/if}
</form>