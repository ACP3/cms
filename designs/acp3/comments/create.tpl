{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}#comments" method="post" accept-charset="UTF-8">
	<fieldset class="no-border">
		<dl>
			<dt><label for="name">{lang t="common|name"}</label></dt>
			<dd><input type="text" name="name" id="name" maxlength="20" value="{$form.name}" required{$form.name_disabled}></dd>
		</dl>
		<dl>
			<dt><label for="message">{lang t="common|message"}</label></dt>
			<dd>
				{if isset($emoticons)}{$emoticons}{/if}
				<textarea name="message" id="message" cols="50" rows="5" required>{$form.message}</textarea>
			</dd>
		</dl>
{$captcha}
	</fieldset>
	<div class="form-bottom">
		<input type="hidden" name="module" value="{$form.module}">
		<input type="hidden" name="entry_id" value="{$form.entry_id}">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		<input type="reset" value="{lang t="common|reset"}" class="form">
		{$form_token}
	</div>
</form>