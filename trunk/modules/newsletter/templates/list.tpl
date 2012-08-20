{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="mail" class="control-label">{lang t="common|email"}</label>
		<div class="controls">
			<input type="email" name="mail" id="mail" maxlength="120" value="{$form.mail}" required><br>
{foreach $actions as $row}
			<label for="{$row.value}" class="radio inline">
				<input type="radio" name="action" id="{$row.value}" value="{$row.value}"{$row.checked}>
				{$row.lang}
			</label>
{/foreach}
		</div>
	</div>
{$captcha}
	<div class="form-actions">
		<button type="submit" name="submit" class="btn">{lang t="common|submit"}</button>
		{$form_token}
	</div>
</form>