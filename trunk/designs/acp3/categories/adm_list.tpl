<form action="{uri args="acp/categories/delete"}" method="post">
	<div id="adm-list">
		{check_access mode="link" action="categories|create" uri="acp/categories/create" icon="32/folder_new" width="32" height="32"}
		{check_access mode="link" action="categories|settings" uri="acp/categories/settings" icon="32/advancedsettings" width="32" height="32"}
		{check_access mode="input" action="categories|delete" icon="32/cancel" lang="common|delete_marked"}
		<h2>{lang t="common|overview"}</h2>
	</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($categories)}
{$pagination}
{assign var="can_delete" value=modules::check("categories", "delete")}
	<table class="acp-table">
		<thead>
			<tr>
{if $can_delete === true}
				<th><input type="checkbox" id="mark-all" value="1" class="checkbox"></th>
{/if}
				<th>{lang t="common|name"}</th>
				<th>{lang t="common|description"}</th>
				<th>{lang t="categories|module"}</th>
				<th style="width:3%">{lang t="common|id"}</th>
			</tr>
		</thead>
		<tbody>
{foreach $categories as $row}
			<tr>
{if $can_delete === true}
				<td><input type="checkbox" name="entries[]" value="{$row.id}" class="checkbox"></td>
{/if}
				<td>{check_access mode="link" action="categories|edit" uri="acp/categories/edit/id_`$row.id`" title=$row.name}</td>
				<td>{$row.description}</td>
				<td>{$row.module}</td>
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
		<h5>{lang t="common|entries"}</h5>
	</div>
{/if}
</form>