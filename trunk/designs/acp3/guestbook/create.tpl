{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<fieldset class="no-border">
		<dl>
			<dt><label for="name">{lang t="common|name"}</label></dt>
			<dd><input type="text" name="name" id="name" size="35" value="{$form.name}" required{$form.name_disabled}></dd>
			<dt><label for="mail">{lang t="common|email"}</label></dt>
			<dd><input type="email" name="mail" id="mail" size="35" value="{$form.mail}"{$form.mail_disabled}></dd>
			<dt><label for="website">{lang t="common|website"}</label></dt>
			<dd><input type="url" name="website" id="website" size="35" value="{$form.website}"{$form.website_disabled}></dd>
			<dt><label for="message">{lang t="common|message"}</label></dt>
			<dd>
				{if isset($emoticons)}{$emoticons}{/if}
				<textarea name="message" id="message" cols="50" rows="6" required>{$form.message}</textarea>
			</dd>
		</dl>
{if isset($subscribe_newsletter)}
<div style="margin:10px 0 0">
	<label for="subscribe-newsletter">
		<input type="checkbox" name="subscribe_newsletter" id="subscribe-newsletter" value="1" class="checkbox"{$subscribe_newsletter}>
		{$LANG_subscribe_to_newsletter}
	</label>
</div>
{/if}
{$captcha}
	</fieldset>
	<div class="form-bottom">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		<input type="reset" value="{lang t="common|reset"}" class="form">
		{$form_token}
	</div>
</form>