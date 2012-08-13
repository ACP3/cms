{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="name" class="control-label">{lang t="common|name"}</label>
		<div class="controls"><input type="text" name="name" id="name" size="35" value="{$form.name}" required{$form.name_disabled}></div>
	</div>
	<div class="control-group">
		<label for="mail" class="control-label">{lang t="common|email"}</label>
		<div class="controls"><input type="email" name="mail" id="mail" size="35" value="{$form.mail}"{$form.mail_disabled}></div>
	</div>
	<div class="control-group">
		<label for="website" class="control-label">{lang t="common|website"}</label>
		<div class="controls"><input type="url" name="website" id="website" size="35" value="{$form.website}"{$form.website_disabled}></div>
	</div>
	<div class="control-group">
		<label for="message" class="control-label">{lang t="common|message"}</label>
		<div class="controls">
			{if isset($emoticons)}{$emoticons}{/if}
			<textarea name="message" id="message" cols="50" rows="6" class="span6" required>{$form.message}</textarea>
		</div>
	</div>
{if isset($subscribe_newsletter)}
	<div class="control-group">
		<div class="controls">
			<label for="subscribe-newsletter" class="checkbox">
				<input type="checkbox" name="subscribe_newsletter" id="subscribe-newsletter" value="1"{$subscribe_newsletter}>
				{$LANG_subscribe_to_newsletter}
			</label>
		</div>
	</div>
{/if}
{$captcha}
	<div class="form-actions">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="btn">
		<input type="reset" value="{lang t="common|reset"}" class="btn">
		{$form_token}
	</div>
</form>