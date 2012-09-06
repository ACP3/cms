<br />
{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}#comments" method="post" accept-charset="UTF-8" class="form-horizontal" style="width:65%;margin:0 auto">
	<div class="control-group">
		<label for="name" class="control-label">{lang t="system|name"}</label>
		<div class="controls"><input type="text" name="name" id="name" maxlength="20" value="{$form.name}" required{$form.name_disabled}></div>
	</div>
	<div class="control-group">
		<label for="message" class="control-label">{lang t="system|message"}</label>
		<div class="controls">
			{if isset($emoticons)}{$emoticons}{/if}
			<textarea name="message" id="message" cols="50" rows="5" class="span10" required>{$form.message}</textarea>
		</div>
	</div>
{if isset($captcha)}
{$captcha}
{/if}
	<div class="form-actions">
		<input type="hidden" name="module" value="{$form.module}">
		<input type="hidden" name="entry_id" value="{$form.entry_id}">
		<button type="submit" name="submit" class="btn">{lang t="system|submit"}</button>
		{$form_token}
	</div>
</form>