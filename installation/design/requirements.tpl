<fieldset>
	<legend>{lang t="installation|step_3_legend_1"}</legend>
	<p>
		{lang t="installation|step_3_paragraph_1"}
	</p>
	<table class="table table-condensed" style="width:auto">
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
	<ul>
{foreach $files_dirs as $row}
		<li>
			<strong>{$row.path}</strong>
			<span style="color:#{$row.color_1}">{$row.exists}</span>, <span style="color:#{$row.color_2}">{$row.writeable}</span>
		</li>
{/foreach}
	</ul>
</fieldset>
<fieldset>
	<legend>{lang t="installation|step_3_legend_2"}</legend>
	<p>
		{lang t="installation|step_3_paragraph_2"}
	</p>
	<ul>
{foreach $php_settings as $row}
		<li>
			<strong>{$row.setting}</strong>
			<span style="color:#{$row.color}">{$row.value}</span>
		</li>
{/foreach}
	</ul>
</fieldset>
<div class="form-actions" style="text-align:center">
{if isset($stop_install)}
{lang t="installation|stop_installation"}
{elseif isset($check_again)}
	<a href="{$REQUEST_URI}" class="btn">{lang t="installation|check_again"}</a>
{else}
	<a href="{uri args="install/configuration"}" class="btn">{lang t="installation|configuration"}</a>
{/if}
</div>