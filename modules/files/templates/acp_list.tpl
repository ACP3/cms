<form action="{uri args="acp/files/delete"}" method="post">
	<div id="adm-list" class="navbar">
		<div class="navbar-inner navbar-text">
			<h2>{lang t="system|overview"}</h2>
			{check_access mode="link" path="acp/files/create" icon="32/download" width="32" height="32"}
			{check_access mode="link" path="acp/files/settings" icon="32/advancedsettings" width="32" height="32"}
			{check_access mode="input" path="acp/files/delete" icon="32/cancel" lang="system|delete_marked"}
		</div>
	</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($files)}
	<table id="acp-table" class="table table-striped table-hover">
		<thead>
			<tr>
{if $can_delete === true}
				<th style="width:3%"><input type="checkbox" id="mark-all" value="1"></th>
{/if}
				<th style="width:22%">{lang t="system|publication_period"}</th>
				<th>{lang t="files|title"}</th>
				<th>{lang t="files|filename"}</th>
				<th>{lang t="files|filesize"}</th>
				<th style="width:5%">{lang t="system|id"}</th>
			</tr>
		</thead>
		<tbody>
{foreach $files as $row}
			<tr>
{if $can_delete === true}
				<td><input type="checkbox" name="entries[]" value="{$row.id}"></td>
{/if}
				<td>{$row.period}</td>
				<td>{check_access mode="link" path="acp/files/edit/id_`$row.id`" title=$row.title}</td>
				<td>{check_access mode="link" path="files/details/id_`$row.id`/action_download" lang="files|download_file" title=$row.file}</td>
				<td>{$row.size}</td>
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