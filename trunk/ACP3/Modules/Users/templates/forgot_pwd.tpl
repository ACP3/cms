{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="nick-mail" class="control-label">{lang t="users|nickname_or_email"}</label>
		<div class="controls">
			<input type="text" name="nick_mail" id="nick-mail" value="{$form.nick_mail}" maxlength="120">
			<p class="help-block">{lang t="users|forgot_pwd_description"}</p>
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