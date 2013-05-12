{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="mail" class="control-label">{lang t="system|email_address"}</label>
		<div class="controls">
			<input type="email" name="mail" id="mail" maxlength="120" value="{$form.mail}" required><br>
{foreach $actions as $row}
			<label for="{$row.id}" class="radio inline">
				<input type="radio" name="action" id="{$row.id}" value="{$row.value}"{$row.checked}>
				{$row.lang}
			</label>
{/foreach}
		</div>
	</div>
{if isset($captcha)}
{$captcha}
{/if}
	<div class="form-actions">
		<button type="submit" name="submit" class="btn">{lang t="system|submit"}</button>
		<a href="{uri args="newsletter/archive"}" class="btn btn-link">{lang t="newsletter|missed_out_newsletter"}</a>
		{$form_token}
	</div>
</form>