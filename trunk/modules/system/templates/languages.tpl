<table class="acp-table">
	<thead>
		<tr>
			<th>{lang t="system|language"}</th>
			<th>{lang t="common|description"}</th>
			<th>{lang t="common|author"}</th>
			<th>{lang t="system|version"}</th>
			<th>{lang t="common|options"}</th>
		</tr>
	</thead>
	<tbody>
{foreach $languages as $row}
		<tr>
			<td>{$row.name}</td>
			<td>{$row.description}</td>
			<td>{$row.author}</td>
			<td>{$row.version}</td>
			<td>
{if $row.selected == 1}
				{icon path="16/apply"}
{else}
				<a href="{uri args="acp/system/languages/dir_`$row.dir`"}">{icon path="16/cancel"}</a>
{/if}
			</td>
		</tr>
{/foreach}
	</tbody>
</table>