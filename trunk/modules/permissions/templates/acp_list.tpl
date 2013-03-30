<form action="{uri args="acp/permissions/delete"}" method="post">
	<div id="adm-list" class="navbar">
		<div class="navbar-inner navbar-text">
			<h2 class="brand">{lang t="system|overview"}</h2>
			{check_access mode="link" path="acp/permissions/create" icon="32/add_group" width="32" height="32"}
			{check_access mode="link" path="acp/permissions/list_resources" icon="32/resource" width="32" height="32"}
			{check_access mode="input" path="acp/permissions/delete" icon="32/cancel" lang="system|delete_marked"}
		</div>
	</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($roles)}
	<table class="table table-striped table-hover">
		<thead>
			<tr>
{if $can_delete === true}
				<th style="width:3%"><input type="checkbox" id="mark-all" value="1"></th>
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
				<td>{$row.spaces}{check_access mode="link" path="acp/permissions/edit/id_`$row.id`" title=$row.name}</td>
{if $can_order === true}
				<td>
{if !$row.last}
					<a href="{uri args="acp/permissions/order/id_`$row.id`/action_down"}" title="{lang t="system|move_down"}">{icon path="16/down" width="16" height="16" alt={lang t="system|move_down"}}</a>
{/if}
{if !$row.first}
					<a href="{uri args="acp/permissions/order/id_`$row.id`/action_up"}" title="{lang t="system|move_up"}">{icon path="16/up" width="16" height="16" alt={lang t="system|move_up"}}</a>
{/if}
{if $row.first && $row.last}
					{icon path="16/editdelete" width="16" height="16" alt={lang t="system|move_impossible"} title={lang t="system|move_impossible"}}
{/if}
				</td>
{/if}
				<td>{$row.id}</td>
			</tr>
{/foreach}
		</tbody>
	</table>
{if $can_delete === true}
{mark name="entries"}
{/if}
{else}
	<div class="alert align-center">
		<strong>{lang t="system|no_entries"}</strong>
	</div>
{/if}
</form>