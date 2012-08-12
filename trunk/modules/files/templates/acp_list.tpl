<form action="{uri args="acp/files/delete"}" method="post">
	<div id="adm-list" class="well">
		{check_access mode="link" path="acp/files/create" icon="32/download" width="32" height="32"}
		{check_access mode="link" path="acp/files/settings" icon="32/advancedsettings" width="32" height="32"}
		{check_access mode="input" path="acp/files/delete" icon="32/cancel" lang="common|delete_marked"}
		<h2>{lang t="common|overview"}</h2>
	</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($files)}
{$pagination}
	<table class="table table-striped">
		<thead>
			<tr>
{if $can_delete === true}
				<th><input type="checkbox" id="mark-all" value="1" class="checkbox inline"></th>
{/if}
				<th>{lang t="common|publication_period"}</th>
				<th>{lang t="files|link_title"}</th>
				<th>{lang t="files|filename"}</th>
				<th>{lang t="files|filesize"}</th>
				<th style="width:3%">{lang t="common|id"}</th>
			</tr>
		</thead>
		<tbody>
{foreach $files as $row}
			<tr>
{if $can_delete === true}
				<td><input type="checkbox" name="entries[]" value="{$row.id}" class="checkbox inline"></td>
{/if}
				<td>{$row.period}</td>
				<td>{check_access mode="link" path="acp/files/edit/id_`$row.id`" title=$row.link_title}</td>
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
	<div class="alert alert-block">
		<h5>{lang t="common|no_entries"}</h5>
	</div>
{/if}
</form>