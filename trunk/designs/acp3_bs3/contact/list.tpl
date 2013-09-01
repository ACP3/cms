{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="form-group">
		<label for="name" class="col-lg-2 control-label">{lang t="system|name"}</label>
		<div class="col-lg-10"><input class="form-control" type="text" name="name" id="name" maxlength="20" value="{$form.name}" required{$form.name_disabled}></div>
	</div>
	<div class="form-group">
		<label for="mail" class="col-lg-2 control-label">{lang t="system|email_address"}</label>
		<div class="col-lg-10"><input class="form-control" type="email" name="mail" id="mail" maxlength="120" value="{$form.mail}" required{$form.mail_disabled}></div>
	</div>
	<div class="form-group">
		<label for="message" class="col-lg-2 control-label">{lang t="system|message"}</label>
		<div class="col-lg-10"><textarea class="form-control" name="message" id="message" cols="50" rows="5" class="input-xxlarge" required>{$form.message}</textarea></div>
	</div>
	<div class="form-group">
		<div class="col-lg-offset-2 col-lg-10">
			<div class="checkbox">
			<label for="copy">
				<input type="checkbox" name="copy" id="copy" value="1"{$copy_checked}>
				{lang t="contact|send_copy_to_sender"}
			</label>
			</div>
		</div>
	</div>
{if isset($captcha)}
{$captcha}
{/if}
	<div class="form-group">
		<div class="col-lg-offset-2 col-lg-10">
			<button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
			{$form_token}
		</div>
	</div>
</form>