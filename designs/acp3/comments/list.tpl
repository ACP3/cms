<h1 id="comments">{lang t="comments|comments"}</h1>
{if isset($comments)}
{$pagination}
{foreach $comments as $row}
<div class="comments">
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
		<strong>{lang t="common|date"}:</strong> {$row.date}
	</div>
	<div class="content">
		{$row.message}
	</div>
	<div class="footer"></div>
</div>
<br>
<br>
{/foreach}
{else}
<div class="error">
	<h5>{lang t="common|no_entries"}</h5>
</div>
{/if}
{if modules::check('comments', 'create')}
<hr>
{/if}