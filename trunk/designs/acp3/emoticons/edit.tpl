{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
	<fieldset>
		<legend>{lang t="emoticons|edit"}</legend>
		<dl>
			<dt><label for="code">{lang t="emoticons|code"}</label></dt>
			<dd><input type="text" name="form[code]" id="code" value="{$form.code}" maxlength="10"></dd>
			<dt><label for="description">{lang t="common|description"}</label></dt>
			<dd><input type="text" name="form[description]" id="description" value="{$form.description}" maxlength="15"></dd>
			<dt><label for="picture">{lang t="emoticons|replace_picture"}</label></dt>
			<dd><input type="file" name="picture" id="picture"></dd>
		</dl>
	</fieldset>
	<div class="form-bottom">
		<input type="submit" value="{lang t="common|submit"}" class="form">
		{$form_token}
	</div>
</form>