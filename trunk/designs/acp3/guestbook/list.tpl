<script type="text/javascript" src="{$DESIGN_PATH}guestbook/script.js"></script>
<div style="text-align:center">
	<a href="{uri args="guestbook/create"}" id="create-link" title="{lang t="guestbook|create"}">{lang t="guestbook|create"}</a>
</div>
{if isset($guestbook)}
{$pagination}
{foreach $guestbook as $row}
<div id="gb-entry-{$row.id}" class="gb">
	<div class="header">
		<div class="author">
			{lang t="common|author"}
		</div>
		<div class="message">
			{lang t="common|message"}
		</div>
	</div>
	<div class="left">
		<strong>{lang t="common|name"}:</strong> {if !empty($row.user_id)}<a href="{uri args="users/view_profile/id_`$row.user_id`"}" title="{lang t="users|view_profile"}">{$row.name}</a>{else}{$row.name}{/if}<br>
		<strong>{lang t="common|date"}:</strong> {$row.date}<br>
{if $row.website != ''}
		<a href="{$row.website}" onclick="window.open(this.href); return false" title="{lang t="guestbook|visit_website"}">{icon path="16/gohome" width="16" height="16" alt="`$row.website`"}</a>
{/if}
{if $row.mail != ''}
		<a href="mailto:{$row.mail}" title="{lang t="guestbook|send_email"}">{icon path="16/mail" width="16" height="16" alt="`$row.mail`"}</a>
{/if}
	</div>
	<div class="content">
		{$row.message}
	</div>
	<div class="footer"></div>
</div>
{/foreach}
{else}
<div class="error">
	<h5>{lang t="common|no_entries"}</h5>
</div>
{/if}