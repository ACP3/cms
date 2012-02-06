<form action="{uri args="acp/access/delete"}" method="post">
	<div id="adm-list">
		{check_access mode="link" action="access|create" uri="acp/access/create" icon="32/add_group" width="32" height="32"}
		{check_access mode="link" action="access|adm_list_resources" uri="acp/access/adm_list_resources" icon="32/resource" width="32" height="32"}
		{check_access mode="input" action="access|delete" icon="32/cancel" lang="common|delete_marked"}
		<h2>{lang t="common|overview"}</h2>
	</div>
	<hr>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($roles)}
{assign var="can_delete" value=modules::check("access", "delete")}
{assign var="can_order" value=modules::check("access", "order")}
	<table class="acp-table">
		<thead>
			<tr>
{if $can_delete === true}
				<th><input type="checkbox" id="mark-all" value="1" class="checkbox"></th>
{/if}
				<th>{lang t="common|name"}</th>
{if $can_order === true}
				<th>{lang t="common|order"}</th>
{/if}
				<th style="width:3%">{lang t="common|id"}</th>
			</tr>
		</thead>
		<tbody>
{foreach $roles as $row}
			<tr>
{if $can_delete === true}
				<td><input type="checkbox" name="entries[]" value="{$row.id}" class="checkbox"></td>
{/if}
				<td style="text-align:left">{check_access mode="link" action="access|edit" uri="acp/access/edit/id_`$row.id`" title=$row.name}</td>
{if $can_order === true}
				<td>
{if !$row.last}
					<a href="{uri args="acp/access/order/id_`$row.id`/action_down"}" title="{lang t="common|move_down"}">{icon path="16/down" width="16" height="16" alt="{lang t="common|move_down"}"}</a>
{/if}
{if !$row.first}
					<a href="{uri args="acp/access/order/id_`$row.id`/action_up"}" title="{lang t="common|move_up"}">{icon path="16/up" width="16" height="16" alt="{lang t="common|move_up"}"}</a>
{/if}
{if $row.first && $row.last}
					{icon path="16/editdelete" width="16" height="16" alt="{lang t="common|move_impossible"}" title="{lang t="common|move_impossible"}"}
{/if}
				</td>
{/if}
				<td>{$row.id}</td>
			</tr>
{/foreach}
		</tbody>
	</table>
{if $can_delete === true}
{mark name="entries"}
{/if}
{else}
	<div class="error">
		<h5>{lang t="common|no_entries"}</h5>
	</div>
{/if}
</form>