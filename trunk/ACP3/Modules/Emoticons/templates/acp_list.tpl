<form action="{uri args="acp/emoticons/delete"}" method="post">
	<div id="adm-list" class="navbar">
		<div class="navbar-inner navbar-text">
			<h2 class="brand">{lang t="system|overview"}</h2>
			{check_access mode="link" path="acp/emoticons/create" icon="32/ksmiletris" width="32" height="32"}
			{check_access mode="link" path="acp/emoticons/settings" icon="32/advancedsettings" width="32" height="32"}
			{check_access mode="input" path="acp/emoticons/delete" icon="32/cancel" lang="system|delete_marked"}
		</div>
	</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($emoticons)}
	<table id="acp-table" class="table table-striped table-hover">
		<thead>
			<tr>
{if $can_delete === true}
				<th style="width:3%"><input type="checkbox" id="mark-all" value="1"></th>
{/if}
				<th>{lang t="system|description"}</th>
				<th>{lang t="emoticons|code"}</th>
				<th>{lang t="emoticons|picture"}</th>
				<th style="width:5%">{lang t="system|id"}</th>
			</tr>
		</thead>
		<tbody>
{foreach $emoticons as $row}
			<tr>
{if $can_delete === true}
				<td><input type="checkbox" name="entries[]" value="{$row.id}"></td>
{/if}
				<td>{check_access mode="link" path="acp/emoticons/edit/id_`$row.id`" title=$row.description}</td>
				<td>{$row.code}</td>
				<td><img src="{$ROOT_DIR}uploads/emoticons/{$row.img}" alt=""></td>
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
		<strong>{lang t="system|no_entries"}</strong>
	</div>
{/if}
</form>