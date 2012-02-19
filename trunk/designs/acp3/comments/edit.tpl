{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<fieldset>
		<legend>{lang t="comments|edit"}</legend>
		<dl>
{if isset($form.user_id) && $form.user_id == '0'}
			<dt><label for="name">{lang t="common|name"}</label></dt>
			<dd><input type="text" name="form[name]" id="name" value="{$form.name}" required></dd>
{/if}
			<dt><label for="message">{lang t="common|message"}</label></dt>
			<dd>
				{if isset($emoticons)}{$emoticons}{/if}
				<textarea name="form[message]" id="message" cols="50" rows="5" required>{$form.message}</textarea>
			</dd>
		</dl>
	</fieldset>
	<div class="form-bottom">
{if isset($form.user_id) && $form.user_id != '0'}
		<input type="hidden" name="form[user_id]" value="{$form.user_id}">
{/if}
		<input type="submit" value="{lang t="common|submit"}" class="form">
		{$form_token}
	</div>
</form>