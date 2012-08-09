{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<fieldset>
		<legend>{lang t="access|acp_edit_resource"}</legend>
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
		{$form_token}
	</div>
</form>