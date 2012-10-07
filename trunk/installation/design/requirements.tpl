<div class="row-fluid">
	<div class="span7">
		<fieldset>
			<legend>{lang t="step_3_legend_1"}</legend>
			<p>
				{lang t="step_3_paragraph_1"}
			</p>
			<div class="row-fluid">
				<div class="span6">
					<table class="table table-condensed">
						<thead>
							<tr>
								<th></th>
								<th style="width:33%">{lang t="required"}</th>
								<th style="width:33%">{lang t="found"}</th>
							</tr>
						</thead>
						<tbody>
{foreach $requirements as $row}
							<tr>
								<td>{$row.name}</td>
								<td>{$row.required}</td>
								<td><span style="color:#{$row.color}">{$row.found}{if $row.color == 'f00'} - {lang t="installation_impossible"}{/if}</span></td>
							</tr>
{/foreach}
						</tbody>
					</table>
				</div>
				<div class="span6">
					<ul class="unstyled">
{foreach $files_dirs as $row}
						<li>
							<strong>{$row.path}</strong>
							<span class="label label-{$row.class_1}">{$row.exists}</span>
							<span class="label label-{$row.class_2}">{$row.writable}</span>
						</li>
{/foreach}
					</ul>
				</div>			
			</div>
		</fieldset>
	</div>
	<div class="span5">
		<fieldset>
			<legend>{lang t="step_3_legend_2"}</legend>
			<p>
				{lang t="step_3_paragraph_2"}
			</p>
			<ul class="unstyled">
{foreach $php_settings as $row}
				<li>
					<strong>{$row.setting}</strong>
					<span class="label label-{$row.class}">{$row.value}</span>
				</li>
{/foreach}
			</ul>
		</fieldset>
	</div>
</div>
{if isset($stop_install)}
<div class="alert alert-warning" style="text-align:center">
	<strong>{lang t="stop_installation"}</strong>
</div>
{else}
<div class="form-actions" style="text-align:center">
{if isset($check_again)}
	<a href="{$REQUEST_URI}" class="btn">{lang t="check_again"}</a>
{else}
	<a href="{uri args="install/configuration"}" class="btn">{lang t="configuration"}</a>
{/if}
</div>
{/if}