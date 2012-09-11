<form action="{uri args="acp/categories/delete"}" method="post">
	<div id="adm-list" class="navbar">
		<div class="navbar-inner navbar-text">
			<h2>{lang t="system|overview"}</h2>
			{check_access mode="link" path="acp/categories/create" icon="32/folder_new" width="32" height="32"}
			{check_access mode="link" path="acp/categories/settings" icon="32/advancedsettings" width="32" height="32"}
			{check_access mode="input" path="acp/categories/delete" icon="32/cancel" lang="system|delete_marked"}
		</div>
	</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($categories)}
	<table id="acp-table" class="table table-striped">
		<thead>
			<tr>
{if $can_delete === true}
				<th style="width:3%"><input type="checkbox" id="mark-all" value="1"></th>
{/if}
				<th>{lang t="system|name"}</th>
				<th>{lang t="system|description"}</th>
				<th>{lang t="categories|module"}</th>
				<th style="width:5%">{lang t="system|id"}</th>
			</tr>
		</thead>
		<tbody>
{foreach $categories as $row}
			<tr>
{if $can_delete === true}
				<td><input type="checkbox" name="entries[]" value="{$row.id}"></td>
{/if}
				<td>{check_access mode="link" path="acp/categories/edit/id_`$row.id`" title=$row.name}</td>
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
	<div class="alert align-center">
		<h5>{lang t="system|entries"}</h5>
	</div>
{/if}
</form>