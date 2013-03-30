<form action="{uri args="acp/newsletter/delete"}" method="post">
	<div id="adm-list" class="navbar">
		<div class="navbar-inner navbar-text">
			<h2 class="brand">{lang t="system|overview"}</h2>
			{check_access mode="link" path="acp/newsletter/create" icon="32/mail_new" width="32" height="32"}
			{check_access mode="link" path="acp/newsletter/list_accounts" icon="32/personal" width="32" height="32"}
			{check_access mode="link" path="acp/newsletter/settings" icon="32/advancedsettings" width="32" height="32"}
			{check_access mode="input" path="acp/newsletter/delete" icon="32/cancel" lang="system|delete_marked"}
		</div>
	</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($newsletter)}
	<table id="acp-table" class="table table-striped table-hover">
		<thead>
			<tr>
{if $can_delete === true}
				<th style="width:3%"><input type="checkbox" id="mark-all" value="1"></th>
{/if}
				<th style="width:22%">{lang t="system|date"}</th>
				<th>{lang t="newsletter|subject"}</th>
				<th>{lang t="newsletter|status"}</th>
{if $can_send}
				<th>{lang t="system|options"}</th>
{/if}
				<th style="width:5%">{lang t="system|id"}</th>
			</tr>
		</thead>
		<tbody>
{foreach $newsletter as $row}
			<tr>
{if $can_delete === true}
				<td><input type="checkbox" name="entries[]" value="{$row.id}"></td>
{/if}
				<td>{$row.date_formatted}</td>
				<td>{check_access mode="link" path="acp/newsletter/edit/id_`$row.id`" title=$row.title}</td>
				<td>{$row.status}</td>
{if $can_send}
				<td><a href="{uri args="acp/newsletter/send/id_`$row.id`"}" title="{lang t="newsletter|acp_send"}">{icon path="16/mail_send" width="16" height="16" alt="{lang t="newsletter|send"}"}</a></td>
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