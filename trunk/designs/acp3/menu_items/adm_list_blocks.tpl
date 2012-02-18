<form action="{uri args="acp/menu_items/delete_blocks"}" method="post">
	<div id="adm-list">
		{check_access mode="link" action="menu_items|create_block" uri="acp/menu_items/create_block" icon="32/source_moc" width="32" height="32"}
		{check_access mode="input" action="menu_items|delete_blocks" icon="32/cancel" lang="common|delete_marked"}
		<h2>{lang t="common|overview"}</h2>
	</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($blocks)}
{$pagination}
{assign var="can_delete" value=modules::check("menu_items", "delete_blocks")}
	<table class="acp-table">
		<thead>
			<tr>
{if $can_delete === true}
				<th><input type="checkbox" id="mark-all" value="1" class="checkbox"></th>
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
				<td><input type="checkbox" name="entries[]" value="{$row.id}" class="checkbox"></td>
{/if}
				<td>{check_access mode="link" action="menu_items|edit_block" uri="acp/menu_items/edit_block/id_`$row.id`" title=$row.title}</td>
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
	<div class="error-box">
		<h5>{lang t="common|no_entries"}</h5>
	</div>
{/if}
</form>