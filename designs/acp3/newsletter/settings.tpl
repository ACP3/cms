{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<fieldset>
		<legend>{lang t="newsletter|settings"}</legend>
		<dl>
			<dt><label for="mail">{lang t="common|email"}</label></dt>
			<dd><input type="email" name="mail" id="mail" value="{$form.mail}" maxlength="120"></dd>
		</dl>
		<dl>
			<dt><label for="mailsig">{lang t="newsletter|mailsig"}</label></dt>
			<dd><textarea name="mailsig" id="mailsig" cols="50" rows="3" style="height:100px">{$form.mailsig}</textarea></dd>
		</dl>
	</fieldset>
	<div class="form-bottom">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		{$form_token}
	</div>
</form>