<form action="{uri args="acp/users/delete"}" method="post">
	<div id="adm-list">
		{check_access mode="link" action="users|create" uri="acp/users/create" icon="32/add_user" width="32" height="32"}
		{check_access mode="link" action="users|settings" uri="acp/users/settings" icon="32/advancedsettings" width="32" height="32"}
		{check_access mode="input" action="users|delete" icon="32/cancel" lang="common|delete_marked"}
		<h2>{lang t="common|overview"}</h2>
	</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($users)}
{$pagination}
	<table class="acp-table">
		<thead>
			<tr>
{if $can_delete === true}
				<th><input type="checkbox" id="mark-all" value="1" class="checkbox"></th>
{/if}
				<th>{lang t="users|nickname"}</th>
				<th>{lang t="common|email"}</th>
				<th>{lang t="access|roles"}</th>
				<th style="width:3%">{lang t="common|id"}</th>
			</tr>
		</thead>
		<tbody>
{foreach $users as $row}
			<tr>
{if $can_delete === true}
				<td><input type="checkbox" name="entries[]" value="{$row.id}" class="checkbox"></td>
{/if}
				<td>{check_access mode="link" action="users|edit" uri="acp/users/edit/id_`$row.id`" title=$row.nickname}</td>
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
	<div class="error-box">
		<h5>{lang t="common|no_entries"}</h5>
	</div>
{/if}
</form>