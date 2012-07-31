{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<fieldset>
		<legend>{lang t="guestbook|edit"}</legend>
		<dl>
			<dt><label for="name">{lang t="common|name"}</label></dt>
			<dd><input type="text" name="name" id="name" value="{$form.name}" required></dd>
		</dl>
		<dl>
			<dt><label for="message">{lang t="common|message"}</label></dt>
			<dd>
				{if isset($emoticons)}{$emoticons}{/if}
				<textarea name="message" id="message" cols="50" rows="5" required>{$form.message}</textarea>
			</dd>
{if isset($activate)}
		<dl>
			<dt><label for="active-1">{lang t="guestbook|activate_entry"}</label></dt>
			<dd>
{foreach $activate as $row}
				<label for="active-{$row.value}">
					<input type="radio" name="active" id="active-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
					{$row.lang}
				</label>
{/foreach}
			</dd>
		</dl>
{/if}
	</fieldset>
	<div class="form-bottom">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		{$form_token}
	</div>
</form>