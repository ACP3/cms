{if isset($redirect_message)}
{$redirect_message}
{/if}
<table class="table table-striped">
	<thead>
		<tr>
			<th>{lang t="system|name"}</th>
			<th>{lang t="system|description"}</th>
			<th>{lang t="system|author"}</th>
			<th>{lang t="system|version"}</th>
			<th>{lang t="system|options"}</th>
		</tr>
	</thead>
	<tbody>
{foreach $designs as $row}
		<tr>
			<td>{$row.name}</td>
			<td>{$row.description}</td>
			<td>{$row.author}</td>
			<td>{$row.version}</td>
			<td>
{if $row.selected == 1}
				{icon path="16/apply"}
{else}
				<a href="{uri args="acp/system/designs/dir_`$row.dir`"}">{icon path="16/cancel"}</a>
{/if}
			</td>
		</tr>
{/foreach}
	</tbody>
</table>