{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="name" class="control-label">{lang t="common|name"}</label>
		<div class="controls"><input type="text" name="name" id="name" value="{$form.name}" required></div>
	</div>
	<div class="control-group">
		<label for="message" class="control-label">{lang t="common|message"}</label>
		<div class="controls">
			{if isset($emoticons)}{$emoticons}{/if}
			<textarea name="message" id="message" cols="50" rows="5" class="span6" required>{$form.message}</textarea>
		</div>
{if isset($activate)}
	<div class="control-group">
		<label for="active-1" class="control-label">{lang t="guestbook|activate_entry"}</label>
		<div class="controls">
{foreach $activate as $row}
			<label for="active-{$row.value}" class="checkbox">
				<input type="radio" name="active" id="active-{$row.value}" value="{$row.value}"{$row.checked}>
				{$row.lang}
			</label>
{/foreach}
		</div>
	</div>
{/if}
	<div class="form-actions">
		<button type="submit" name="submit" class="btn">{lang t="common|submit"}</button>
		<a href="{uri args="acp/guestbook"}" class="btn">{lang t="common|cancel"}</a>
		{$form_token}
	</div>
</form>