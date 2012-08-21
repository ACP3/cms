<form action="{uri args="acp/static_pages/delete"}" method="post">
	<div id="adm-list" class="well">
		{check_access mode="link" path="acp/static_pages/create" icon="32/contents" width="32" height="32"}
		{check_access mode="input" path="acp/static_pages/delete" icon="32/cancel" lang="common|delete_marked"}
		<h2>{lang t="common|overview"}</h2>
	</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($pages)}
{$pagination}
	<table class="table table-striped">
		<thead>
			<tr>
{if $can_delete === true}
				<th><input type="checkbox" id="mark-all" value="1"></th>
{/if}
				<th>{lang t="common|publication_period"}</th>
				<th>{lang t="static_pages|title"}</th>
				<th style="width:3%">{lang t="common|id"}</th>
			</tr>
		</thead>
		<tbody>
{foreach $pages as $row}
			<tr>
{if $can_delete === true}
				<td><input type="checkbox" name="entries[]" value="{$row.id}"></td>
{/if}
				<td>{$row.period}</td>
				<td>{check_access mode="link" path="acp/static_pages/edit/id_`$row.id`" title=$row.title}</td>
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
		<strong>{lang t="common|no_entries"}</strong>
	</div>
{/if}
</form>