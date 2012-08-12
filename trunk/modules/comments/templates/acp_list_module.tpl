<form action="{uri args="acp/comments/delete_comments_per_module"}" method="post">
	<div id="adm-list" class="well">
		{check_access mode="link" path="acp/comments/settings" icon="32/advancedsettings" width="32" height="32"}
		{check_access mode="input" path="acp/comments/delete_comments_per_module" icon="32/cancel" lang="common|delete_marked"}
		<h2>{lang t="common|overview"}</h2>
	</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($comments)}
{$pagination}
	<table class="table table-striped">
		<thead>
			<tr>
{if $can_delete === true}
				<th><input type="checkbox" id="mark-all" value="1" class="checkbox inline"></th>
{/if}
				<th>{lang t="comments|module"}</th>
				<th>{lang t="comments|comments_count"}</th>
			</tr>
		</thead>
		<tbody>
{foreach $comments as $row}
			<tr>
{if $can_delete === true}
				<td><input type="checkbox" name="entries[]" value="{$row.module}" class="checkbox inline"></td>
{/if}
				<td>{check_access mode="link" path="acp/comments/list/module_`$row.module`" lang="comments|show_comments" title=$row.name}</td>
				<td>{$row.count}</td>
			</tr>
{/foreach}
		</tbody>
	</table>
{if $can_delete === true}
{mark name="entries"}
{/if}
{else}
	<div class="alert alert-block">
		<h5>{lang t="common|no_entries"}</h5>
	</div>
{/if}
</form>