<form action="{uri args="acp/newsletter/delete_archive"}" method="post">
	<div id="adm-list">
		{check_access mode="input" action="newsletter|delete_archive" icon="32/cancel" lang="common|delete_marked"}
		<h2>{lang t="newsletter|newsletter_archive"}</h2>
	</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($newsletter)}
{$pagination}
{assign var="can_delete" value=modules::check("newsletter", "delete_archive")}
{assign var="can_send" value=modules::check("newsletter", "send")}
	<table class="acp-table">
		<thead>
			<tr>
{if $can_delete === true}
				<th><input type="checkbox" id="mark-all" value="1" class="checkbox"></th>
{/if}
				<th>{lang t="common|date"}</th>
				<th>{lang t="newsletter|subject"}</th>
				<th>{lang t="newsletter|text"}</th>
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
				<td><input type="checkbox" name="entries[]" value="{$row.id}" class="checkbox"></td>
{/if}
				<td>{$row.date}</td>
				<td>{check_access mode="link" action="newsletter|edit_archive" uri="acp/newsletter/edit_archive/id_`$row.id`" title=$row.subject}</td>
				<td>{$row.text}</td>
				<td>{$row.status}</td>
{if $can_send}
				<td><a href="{uri args="acp/newsletter/send/id_`$row.id`"}" title="{lang t="newsletter|send"}">{icon path="16/mail_send" width="16" height="16" alt="{lang t="newsletter|send"}"}</a></td>
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
	<div class="error-box">
		<h5>{lang t="common|no_entries"}</h5>
	</div>
{/if}
</form>