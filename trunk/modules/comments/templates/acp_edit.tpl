{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
{if isset($form.user_id) && $form.user_id == '0'}
	<div class="control-group">
		<label for="name" class="control-label">{lang t="system|name"}</label>
		<div class="controls"><input type="text" name="name" id="name" value="{$form.name}" required></div>
	</div>
{/if}
	<div class="control-group">
		<label for="message" class="control-label">{lang t="system|message"}</label>
		<div class="controls">
			{if isset($emoticons)}{$emoticons}{/if}
			<textarea name="message" id="message" cols="50" rows="5" class="span6" required>{$form.message}</textarea>
		</div>
	</div>
	<div class="form-actions">
{if isset($form.user_id) && $form.user_id != '0'}
		<input type="hidden" name="user_id" value="{$form.user_id}">
{/if}
		<button type="submit" name="submit" class="btn">{lang t="system|submit"}</button>
		<a href="{uri args="acp/comments/list_comments/id_`$module_id`"}" class="btn">{lang t="system|cancel"}</a>
		{$form_token}
	</div>
</form>