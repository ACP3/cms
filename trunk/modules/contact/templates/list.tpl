{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="name" class="control-label">{lang t="common|name"}</label>
		<div class="controls"><input type="text" name="name" id="name" maxlength="20" value="{$form.name}" required{$form.name_disabled}></div>
	</div>
	<div class="control-group">
		<label for="mail" class="control-label">{lang t="common|email"}</label>
		<div class="controls"><input type="email" name="mail" id="mail" maxlength="120" value="{$form.mail}" required{$form.mail_disabled}></div>
	</div>
	<div class="control-group">
		<label for="message" class="control-label">{lang t="common|message"}</label>
		<div class="controls"><textarea name="message" id="message" cols="50" rows="5" class="span6" required>{$form.message}</textarea></div>
	</div>
{$captcha}
	<div class="form-actions">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="btn">
		<input type="reset" value="{lang t="common|reset"}" class="btn">
		{$form_token}
	</div>
</form>