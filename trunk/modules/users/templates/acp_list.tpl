<form action="{uri args="acp/users/delete"}" method="post">
	<div id="adm-list" class="navbar">
		<div class="navbar-inner navbar-text">
			<h2>{lang t="system|overview"}</h2>
			{check_access mode="link" path="acp/users/create" icon="32/add_user" width="32" height="32"}
			{check_access mode="link" path="acp/users/settings" icon="32/advancedsettings" width="32" height="32"}
			{check_access mode="input" path="acp/users/delete" icon="32/cancel" lang="system|delete_marked"}
		</div>
	</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($users)}
	<table id="acp-table" class="table table-striped">
		<thead>
			<tr>
{if $can_delete === true}
				<th style="width:3%"><input type="checkbox" id="mark-all" value="1"></th>
{/if}
				<th>{lang t="users|nickname"}</th>
				<th>{lang t="system|email_address"}</th>
				<th>{lang t="permissions|roles"}</th>
				<th style="width:5%">{lang t="system|id"}</th>
			</tr>
		</thead>
		<tbody>
{foreach $users as $row}
			<tr>
{if $can_delete === true}
				<td><input type="checkbox" name="entries[]" value="{$row.id}"></td>
{/if}
				<td>{check_access mode="link" path="acp/users/edit/id_`$row.id`" title=$row.nickname}</td>
				<td>{$row.mail}</td>
				<td>{$row.roles}</td>
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