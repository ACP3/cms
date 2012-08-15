<h2 id="comments" class="align-center" style="margin:5px 0">{lang t="comments|comments"}</h2>
{if isset($comments)}
{$pagination}
{foreach $comments as $row}
<div class="dataset-box" style="width:65%">
	<div class="header">
		<div class="f-right small">{$row.date}</div>
		{if !empty($row.user_id)}<a href="{uri args="users/view_profile/id_`$row.user_id`"}" title="{lang t="users|view_profile"}">{$row.name}</a>{else}{$row.name}{/if}
	</div>
	<div class="content">
		{$row.message}
	</div>
</div>
{/foreach}
{else}
<div class="alert alert-block align-center">
	<h5>{lang t="common|no_entries"}</h5>
</div>
{/if}