<form action="{uri args="acp/comments/delete_comments_per_module"}" method="post">
	<div id="adm-list">
		{check_access mode="link" action="comments|settings" uri="acp/comments/settings" icon="32/advancedsettings" width="32" height="32"}
		{check_access mode="input" action="comments|delete_comments_per_module" icon="32/cancel" lang="common|delete_marked"}
		<h2>{lang t="common|overview"}</h2>
	</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($comments)}
{$pagination}
	<table class="acp-table">
		<thead>
			<tr>
{if $can_delete === true}
				<th><input type="checkbox" id="mark-all" value="1" class="checkbox"></th>
{/if}
				<th>{lang t="comments|module"}</th>
				<th>{lang t="comments|comments_count"}</th>
			</tr>
		</thead>
		<tbody>
{foreach $comments as $row}
			<tr>
{if $can_delete === true}
				<td><input type="checkbox" name="entries[]" value="{$row.module}" class="checkbox"></td>
{/if}
				<td>{check_access mode="link" action="comments|adm_list" uri="acp/comments/adm_list/module_`$row.module`" lang="comments|show_comments" title=$row.name}</td>
				<td>{$row.count}</td>
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