{if isset($export)}
<pre>
{$export}
</pre>
{else}
{if isset($error_msg)}
{$error_msg}
{/if}
<script type="text/javascript" src="{$DESIGN_PATH}system/export.js"></script>
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<div id="tabs">
		<ul>
			<li><a href="#tab-1">{lang t="system|sql_tables"}</a></li>
			<li><a href="#tab-2">{lang t="system|export_options"}</a></li>
		</ul>
		<div id="tab-1">
			<dl>
				<dd>
					<select name="form[tables][]" id="tables" multiple="multiple" style="height:200px">
{foreach $tables as $row}
						<option value="{$row.name}"{$row.selected}>{$row.name}</option>
{/foreach}
					</select>
					<a href="javascript:mark_options('add')">{lang t="common|mark_all"}</a> <span>::</span> <a href="javascript:mark_options('remove')">{lang t="common|unmark_all"}</a>
				</dd>
			</dl>
		</div>
		<div id="tab-2" class="ui-tabs-hide">
			<dl>
				<dt><label for="file">{lang t="system|output"}</label></dt>
				<dd>
{foreach $output as $row}
					<label for="{$row.value}">
						<input type="radio" name="form[output]" id="{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
						{$row.lang}
					</label>
{/foreach}
				</dd>
				<dt><label for="complete">{lang t="system|export_type"}</label></dt>
				<dd>
{foreach $export_type as $row}
					<label for="{$row.value}">
						<input type="radio" name="form[export_type]" id="{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
						{$row.lang}
					</label>
{/foreach}
				</dd>
			</dl>
			<dl id="options-container">
				<dt><label for="drop">{lang t="common|options"}</label></dt>
				<dd>
					<label for="drop">
						<input type="checkbox" name="form[drop]" id="drop" value="1" class="checkbox"{$drop.checked}>
						{$drop.lang}
					</label>
				</dd>
			</dl>
		</div>
	</div>
	<div class="form-bottom">
		<input type="submit" value="{lang t="common|submit"}" class="form">
	</div>
</form>
{/if}