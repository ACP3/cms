<p>
	{$LANG_users_found}
</p>
{if isset($users)}
{$pagination}
<table class="table table-striped">
	<thead>
		<tr>
			<th>{lang t="users|nickname"}</th>
			<th>{lang t="users|realname"}</th>
			<th>{lang t="common|email"}</th>
			<th>{lang t="common|website"}</th>
		</tr>
	</thead>
	<tbody>
{foreach $users as $row}
		<tr>
			<td><a href="{uri args="users/view_profile/id_`$row.id`"}" title="{lang t="users|view_profile"}">{$row.nickname}</a></td>
			<td>{if $row.realname != '' && $row.realname_display == 1}{$row.realname}{else}-{/if}</td>
			<td>{if $row.mail_display == 1}<a href="mailto:{$row.mail}" title="{lang t="users|send_email"}">{$row.mail}</a>{else}-{/if}</td>
			<td>{if $row.website != '' && $row.website_display == 1}<a href="{$row.website}" onclick="window.open(this.href); return false" title="{lang t="users|visit_website"}">{$row.website}</a>{else}-{/if}</td>
		</tr>
{/foreach}
	</tbody>
</table>
{else}
<div class="alert align-center">
	<strong>{lang t="common|no_entries"}</strong>
</div>
{/if}