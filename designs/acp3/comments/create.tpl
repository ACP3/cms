{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}#comments" method="post" accept-charset="UTF-8">
	<fieldset class="no-border">
		<dl>
			<dt><label for="name">{lang t="common|name"}</label></dt>
			<dd><input type="text" name="form[name]" id="name" maxlength="20" value="{$form.name}"{$form.name_disabled}></dd>
			<dt><label for="message">{lang t="common|message"}</label></dt>
			<dd>
				{if isset($emoticons)}{$emoticons}{/if}
				<textarea name="form[message]" id="message" cols="50" rows="5">{$form.message}</textarea>
			</dd>
		</dl>
{$captcha}
	</fieldset>
	<div class="form-bottom">
		<input type="hidden" name="form[module]" value="{$form.module}">
		<input type="hidden" name="form[entry_id]" value="{$form.entry_id}">
		<input type="submit" value="{lang t="common|submit"}" class="form">
		<input type="reset" value="{lang t="common|reset"}" class="form">
		{$form_token}
	</div>
</form>