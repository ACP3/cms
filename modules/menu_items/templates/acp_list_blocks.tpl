<form action="{uri args="acp/menu_items/delete_blocks"}" method="post">
	<div id="adm-list" class="well">
		{check_access mode="link" path="acp/menu_items/create_block" icon="32/source_moc" width="32" height="32"}
		{check_access mode="input" path="acp/menu_items/delete_blocks" icon="32/cancel" lang="common|delete_marked"}
		<h2>{lang t="common|overview"}</h2>
	</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($blocks)}
{$pagination}
	<table class="table table-striped">
		<thead>
			<tr>
{if $can_delete === true}
				<th><input type="checkbox" id="mark-all" value="1" class="checkbox inline"></th>
{/if}
				<th>{lang t="menu_items|title"}</th>
				<th>{lang t="menu_items|index_name"}</th>
				<th style="width:3%">{lang t="common|id"}</th>
			</tr>
		</thead>
		<tbody>
{foreach $blocks as $row}
			<tr>
{if $can_delete === true}
				<td><input type="checkbox" name="entries[]" value="{$row.id}" class="checkbox inline"></td>
{/if}
				<td>{check_access mode="link" path="acp/menu_items/edit_block/id_`$row.id`" title=$row.title}</td>
				<td>{$row.index_name}</td>
				<td>{$row.id}</td>
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