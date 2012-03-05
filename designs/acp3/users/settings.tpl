{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<fieldset>
		<legend>{lang t="users|settings"}</legend>
		<dl>
			<dt><label for="language-override-1">{lang t="users|allow_language_override"}</label></dt>
			<dd>
{foreach $languages as $row}
				<label for="language-override-{$row.value}">
					<input type="radio" name="language_override" id="language-override-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
					{$row.lang}
				</label>
{/foreach}
			</dd>
			<dt><label for="entries-override-1">{lang t="users|allow_entries_override"}</label></dt>
			<dd>
{foreach $entries as $row}
				<label for="entries-override-{$row.value}">
					<input type="radio" name="entries_override" id="entries-override-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
					{$row.lang}
				</label>
{/foreach}
			</dd>
			<dt><label for="enable-registration-1">{lang t="users|enable_registration"}</label></dt>
			<dd>
{foreach $registration as $row}
				<label for="enable-registration-{$row.value}">
					<input type="radio" name="enable_registration" id="enable-registration-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
					{$row.lang}
				</label>
{/foreach}
			</dd>
		</dl>
	</fieldset>
	<div class="form-bottom">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		{$form_token}
	</div>
</form>