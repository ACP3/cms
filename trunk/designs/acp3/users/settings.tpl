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
					<input type="radio" name="form[language_override]" id="language-override-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
					{$row.lang}
				</label>
{/foreach}
			</dd>
			<dt><label for="entries-override-1">{lang t="users|allow_entries_override"}</label></dt>
			<dd>
{foreach $entries as $row}
				<label for="entries-override-{$row.value}">
					<input type="radio" name="form[entries_override]" id="entries-override-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
					{$row.lang}
				</label>
{/foreach}
			</dd>
		</dl>
	</fieldset>
	<div class="form-bottom">
		<input type="submit" value="{lang t="common|submit"}" class="form">
		{$form_token}
	</div>
</form>