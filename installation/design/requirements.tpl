<fieldset>
	<legend>{lang t="installation|step_3_legend_1"}</legend>
	<p style="margin:0">
		{lang t="installation|step_3_paragraph_1"}
	</p>
	<table class="acp-table">
		<thead>
			<tr>
				<th></th>
				<th style="width:33%">{lang t="installation|required"}</th>
				<th style="width:33%">{lang t="installation|found"}</th>
			</tr>
		</thead>
		<tbody>
{foreach $requirements as $row}
			<tr>
				<td>{$row.name}</td>
				<td>{$row.required}</td>
				<td><span style="color:#{$row.color}">{$row.found}{if $row.color == 'f00'} - {lang t="installation|installation_impossible"}{/if}</span></td>
			</tr>
{/foreach}
		</tbody>
	</table>
	<p>
{foreach $files_dirs as $row}
		<strong>{$row.path}</strong> <span style="color:#{$row.color_1}">{$row.exists}</span>, <span style="color:#{$row.color_2}">{$row.writeable}</span><br>
{/foreach}
	</p>
</fieldset>
<fieldset>
	<legend>{lang t="installation|step_3_legend_2"}</legend>
	<p style="margin:0">
		{lang t="installation|step_3_paragraph_2"}
	</p>
	<p>
{foreach $php_settings as $row}
		<strong>{$row.setting}</strong>
		<span style="color:#{$row.color}">{$row.value}</span><br>
{/foreach}
	</p>
</fieldset>
<br>
<div class="success forward">
{if isset($stop_install)}
{lang t="installation|stop_installation"}
{elseif isset($check_again)}
	<a href="{$REQUEST_URI}" class="form">{lang t="installation|check_again"}</a>
{else}
	<a href="{uri args="install/configuration"}" class="form">{lang t="installation|configuration"}</a>
{/if}
</div>