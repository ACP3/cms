<form action="{uri args="acp/guestbook/delete"}" method="post">
	<div id="adm-list" class="well">
		{check_access mode="link" path="acp/guestbook/settings" icon="32/advancedsettings" width="32" height="32"}
		{check_access mode="input" path="acp/guestbook/delete" icon="32/cancel" lang="common|delete_marked"}
		<h2>{lang t="common|overview"}</h2>
	</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($guestbook)}
{$pagination}
	<table class="table table-striped">
		<thead>
			<tr>
{if $can_delete === true}
				<th><input type="checkbox" id="mark-all" value="1" class="checkbox inline"></th>
{/if}
				<th>{lang t="common|date"}</th>
				<th>{lang t="common|name"}</th>
				<th>{lang t="common|message"}</th>
				<th>{lang t="guestbook|ip"}</th>
				<th style="width:3%">{lang t="common|id"}</th>
			</tr>
		</thead>
		<tbody>
{foreach $guestbook as $row}
			<tr>
{if $can_delete === true}
				<td><input type="checkbox" name="entries[]" value="{$row.id}" class="checkbox inline"></td>
{/if}
				<td>{$row.date}</td>
				<td>{check_access mode="link" path="acp/guestbook/edit/id_`$row.id`" title=$row.name}</td>
				<td>{$row.message}</td>
				<td>{$row.ip}</td>
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