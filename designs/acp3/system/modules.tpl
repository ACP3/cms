<p>
	{$LANG_modules_found}
</p>
<table class="acp-table">
	<thead>
		<tr>
			<th>{lang t="system|module_name"}</th>
			<th>{lang t="common|description"}</th>
			<th>{lang t="common|author"}</th>
			<th>{lang t="system|version"}</th>
			<th>{lang t="common|options"}</th>
		</tr>
	</thead>
	<tbody>
{foreach $modules as $row}
		<tr>
			<td>{$row.name}</td>
			<td>{$row.description}</td>
			<td>{$row.author}</td>
			<td>{$row.version}</td>
			<td>
{if $row.protected === true}
				{icon path="16/editdelete" width="16" height="16"}
{elseif $row.active === true}
				<a href="{uri args="acp/system/modules/dir_`$row.dir`/action_deactivate"}" title="{lang t="system|disable_module"}">{icon path="16/apply" width="16" height="16"}</a>
{else}
				<a href="{uri args="acp/system/modules/dir_`$row.dir`/action_activate"}" title="{lang t="system|enable_module"}">{icon path="16/cancel" width="16" height="16"}</a>
{/if}
			</td>
		</tr>
{/foreach}
	</tbody>
</table>