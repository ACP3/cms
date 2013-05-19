{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="name" class="control-label">{lang t="system|name"}</label>
		<div class="controls"><input type="text" name="name" id="name" maxlength="20" value="{$form.name}" required{$form.name_disabled}></div>
	</div>
	<div class="control-group">
		<label for="mail" class="control-label">{lang t="system|email_address"}</label>
		<div class="controls"><input type="email" name="mail" id="mail" maxlength="120" value="{$form.mail}" required{$form.mail_disabled}></div>
	</div>
	<div class="control-group">
		<label for="message" class="control-label">{lang t="system|message"}</label>
		<div class="controls"><textarea name="message" id="message" cols="50" rows="5" class="input-xxlarge" required>{$form.message}</textarea></div>
	</div>
	<div class="control-group">
		<div class="controls">
			<label for="copy" class="checkbox">
				<input type="checkbox" name="copy" id="copy" value="1"{$copy_checked}>
				{lang t="contact|send_copy_to_sender"}
			</label>
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