{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<fieldset>
		<legend>{lang t="newsletter|acp_settings"}</legend>
		<div class="control-group">
			<label for="mail" class="control-label">{lang t="common|email"}</label>
			<div class="controls"><input type="email" name="mail" id="mail" value="{$form.mail}" maxlength="120"></div>
		</div>
		<div class="control-group">
			<label for="mailsig" class="control-label">{lang t="newsletter|mailsig"}</label>
			<div class="controls"><textarea name="mailsig" id="mailsig" cols="50" rows="3" style="height:100px">{$form.mailsig}</textarea></div>
		</div>
	</fieldset>
	<div class="form-actions">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="btn">
		{$form_token}
	</div>
</form>