{if !isset($tables)}
<div class="error-box" style="padding-top:0">
	<p>
		{lang t="system|sql_table_optimisation_description"}
	</p>
	<strong>{lang t="system|attention"}:</strong> {lang t="system|optimisation_access_forbidden"}
</div>
<div style="margin-top:20px;text-align:center">
	<a href="{uri args="acp/system/sql_optimisation/action_do"}" class="form">{lang t="common|forward"}</a>
</div>
{else}
<table class="acp-table" style="width:66%">
	<thead>
		<tr>
			<th>{lang t="system|table_name"}</th>
			<th>{lang t="system|status"}</th>
			<th>{lang t="system|overhead"}</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="2">{lang t="system|overall_overhead"}:</td>
			<td>{$total_overhead}</td>
		</tr>
	</tfoot>
	<tbody>
{foreach $tables as $table}
		<tr>
			<td>{$table.name}</td>
			<td>{$table.status}</td>
			<td>{$table.overhead}</td>
		</tr>
{/foreach}
	</tbody>
</table>
{/if}