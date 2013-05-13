<br />
{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}#comments" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="name" class="control-label">{lang t="system|name"}</label>
		<div class="controls"><input type="text" name="name" id="name" maxlength="20" value="{$form.name}" required{$form.name_disabled}></div>
	</div>
	<div class="control-group">
		<label for="message" class="control-label">{lang t="system|message"}</label>
		<div class="controls">
			{if isset($emoticons)}{$emoticons}{/if}
			<textarea name="message" id="message" cols="50" rows="5" class="input-xlarge" required>{$form.message}</textarea>
		</div>
	</div>
{if isset($captcha)}
{$captcha}
{/if}
	<div class="form-actions">
		<button type="submit" name="submit" class="btn">{lang t="system|submit"}</button>
		{$form_token}
	</div>
</form>