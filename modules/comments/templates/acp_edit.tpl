{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<fieldset>
		<legend>{lang t="comments|acp_edit"}</legend>
{if isset($form.user_id) && $form.user_id == '0'}
		<dl>
			<dt><label for="name">{lang t="common|name"}</label></dt>
			<dd><input type="text" name="name" id="name" value="{$form.name}" required></dd>
		</dl>
{/if}
		<dl>
			<dt><label for="message">{lang t="common|message"}</label></dt>
			<dd>
				{if isset($emoticons)}{$emoticons}{/if}
				<textarea name="message" id="message" cols="50" rows="5" required>{$form.message}</textarea>
			</dd>
		</dl>
	</fieldset>
	<div class="form-bottom">
{if isset($form.user_id) && $form.user_id != '0'}
		<input type="hidden" name="user_id" value="{$form.user_id}">
{/if}
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		{$form_token}
	</div>
</form>