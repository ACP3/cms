<form action="{uri args="acp/newsletter/delete_archive"}" method="post">
	<div id="adm-list" class="well">
		{check_access mode="link" path="acp/newsletter/create" icon="32/mail_new" width="32" height="32"}
		{check_access mode="link" path="acp/newsletter/list_accounts" icon="32/personal" width="32" height="32"}
		{check_access mode="link" path="acp/newsletter/settings" icon="32/advancedsettings" width="32" height="32"}
		{check_access mode="input" path="acp/newsletter/delete" icon="32/cancel" lang="common|delete_marked"}
		<h2>{lang t="newsletter|newsletter_archive"}</h2>
	</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($newsletter)}
{$pagination}
	<table class="table table-striped">
		<thead>
			<tr>
{if $can_delete === true}
				<th><input type="checkbox" id="mark-all" value="1"></th>
{/if}
				<th>{lang t="common|date"}</th>
				<th>{lang t="newsletter|subject"}</th>
				<th>{lang t="newsletter|status"}</th>
{if $can_send}
				<th>{lang t="common|options"}</th>
{/if}
				<th style="width:3%">{lang t="common|id"}</th>
			</tr>
		</thead>
		<tbody>
{foreach $newsletter as $row}
			<tr>
{if $can_delete === true}
				<td><input type="checkbox" name="entries[]" value="{$row.id}"></td>
{/if}
				<td>{$row.date}</td>
				<td>{check_access mode="link" path="acp/newsletter/edit/id_`$row.id`" title=$row.subject}</td>
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
	<div class="alert alert-block align-center">
		<h5>{lang t="common|no_entries"}</h5>
	</div>
{/if}
</form>