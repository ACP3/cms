{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<fieldset>
		<legend>{lang t="access|create_resource"}</legend>
		<dl>
			<dt><label for="privileges">{lang t="common|module"}</label></dt>
			<dd>
				<select name="modules" id="modules">
{foreach $modules as $row}
					<option value="{$row.dir}"{$row.selected}>{$row.name}</option>
{/foreach}
				</select>
			</dd>
		</dl>
		<dl>
			<dt><label for="resource">{lang t="access|resource_name"}</label></dt>
			<dd><input type="text" name="resource" id="resource" value="{$form.resource}" required></dd>
		</dl>
		<dl>
			<dt><label for="privileges">{lang t="access|assigned_privilege"}</label></dt>
			<dd>
				<select name="privileges" id="privileges">
{foreach $privileges as $row}
					<option value="{$row.id}"{$row.selected}>{$row.key}{if !empty($row.description)} ({$row.description}){/if}</option>
{/foreach}
				</select>
			</dd>
		</dl>
	</fieldset>
	<div class="form-bottom">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		<input type="reset" value="{lang t="common|reset"}" class="form">
		{$form_token}
	</div>
</form>