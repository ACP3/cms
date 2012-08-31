<form action="{uri args="acp/menus/delete_item"}" method="post">
	<div id="adm-list" class="well">
		{check_access mode="link" path="acp/menus/create" icon="32/source_moc" width="32" height="32"}
		{check_access mode="link" path="acp/menus/create_item" icon="32/kmenuedit" width="32" height="32"}
		{check_access mode="input" path="acp/menus/delete" icon="32/cancel" lang="common|delete_marked"}
		<h2>{lang t="common|overview"}</h2>
	</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($pages_list)}
	<table class="table table-striped">
		<thead>
			<tr>
{if $can_delete_item === true}
				<th><input type="checkbox" id="mark-all" value="1"></th>
{/if}
				<th style="width:30%">{lang t="menus|title"}</th>
				<th>{lang t="menus|page_type"}</th>
{if $can_order_item === true}
				<th>{lang t="common|order"}</th>
{/if}
				<th style="width:3%">{lang t="common|id"}</th>
			</tr>
		</thead>
		<tbody>
{foreach $pages_list as $block => $values}
			<tr>
				<td class="sub-table-header" colspan="{$colspan}">
					{$values.title} <span>({lang t="menus|index_name2"} {$block})</span>
{if $can_delete || $can_edit}
					<div class="btn-group pull-right">
{if $can_edit}
						<a href="{uri args="acp/menus/edit/id_`$values.menu_id`"}" class="btn btn-small" title="{lang t="menus|acp_edit"}"><i class="icon-edit"></i> {lang t="common|edit"}</a>
{/if}
{if $can_delete}
						<a href="{uri args="acp/menus/delete/entries_`$values.menu_id`"}" class="btn btn-small" title="{lang t="menus|acp_delete"}"><i class="icon-remove"></i> {lang t="common|delete"}</a>
{/if}
					</div>
{/if}
				</td>
			</tr>
{foreach $values.items as $row}
			<tr>
{if $can_delete_item === true}
				<td><input type="checkbox" name="entries[]" value="{$row.id}"></td>
{/if}
				<td>{$row.spaces}{check_access mode="link" path="acp/menus/edit_item/id_`$row.id`" title=$row.title}</td>
				<td>{$row.mode_formatted}</td>
{if $can_order_item === true}
				<td>
{if !$row.last}
					<a href="{uri args="acp/menus/order_item/id_`$row.id`/action_down"}" title="{lang t="common|move_down"}">{icon path="16/down" width="16" height="16" alt={lang t="common|move_down"}}</a>
{/if}
{if !$row.first}
					<a href="{uri args="acp/menus/order_item/id_`$row.id`/action_up"}" title="{lang t="common|move_up"}">{icon path="16/up" width="16" height="16" alt={lang t="common|move_up"}}</a>
{/if}
{if $row.first && $row.last}
					{icon path="16/editdelete" width="16" height="16" alt={lang t="common|move_impossible"} title={lang t="common|move_impossible"}}
{/if}
				</td>
{/if}
				<td>{$row.id}</td>
			</tr>
{/foreach}
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