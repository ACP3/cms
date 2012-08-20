{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="mail" class="control-label">{lang t="common|email"}</label>
		<div class="controls"><input type="email" name="mail" id="mail" value="{$form.mail}" maxlength="120"></div>
	</div>
	<div class="control-group">
		<label for="mailsig" class="control-label">{lang t="newsletter|mailsig"}</label>
		<div class="controls"><textarea name="mailsig" id="mailsig" cols="50" rows="3" class="span6">{$form.mailsig}</textarea></div>
	</div>
	<div class="form-actions">
		<button type="submit" name="submit" class="btn">{lang t="common|submit"}</button>
		<a href="{uri args="acp/newsletter"}" class="btn">{lang t="common|cancel"}</a>
		{$form_token}
	</div>
</form>