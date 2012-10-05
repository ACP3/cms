<form action="{uri args="acp/news/delete"}" method="post">
	<div id="adm-list" class="navbar">
		<div class="navbar-inner navbar-text">
			<h2>{lang t="system|overview"}</h2>
			{check_access mode="link" path="acp/news/create" icon="32/news" width="32" height="32"}
			{check_access mode="link" path="acp/news/settings" icon="32/advancedsettings" width="32" height="32"}
			{check_access mode="input" path="acp/news/delete" icon="32/cancel" lang="system|delete_marked"}
		</div>
	</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($news)}
	<table id="acp-table" class="table table-striped">
		<thead>
			<tr>
{if $can_delete === true}
				<th style="width:3%"><input type="checkbox" id="mark-all" value="1"></th>
{/if}
				<th style="width:22%">{lang t="system|publication_period"}</th>
				<th>{lang t="news|title"}</th>
				<th>{lang t="categories|category"}</th>
				<th style="width:5%">{lang t="system|id"}</th>
			</tr>
		</thead>
		<tbody>
{foreach $news as $row}
			<tr>
{if $can_delete === true}
				<td><input type="checkbox" name="entries[]" value="{$row.id}"></td>
{/if}
				<td>{$row.period}</td>
				<td>{check_access mode="link" path="acp/news/edit/id_`$row.id`" title=$row.title}</td>
				<td>{$row.cat}</td>
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