{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
	<fieldset>
		<legend>{lang t="emoticons|create"}</legend>
		<dl>
			<dt><label for="code">{lang t="emoticons|code"}</label></dt>
			<dd><input type="text" name="code" id="code" value="{$form.code}" maxlength="10"></dd>
		</dl>
		<dl>
			<dt><label for="description">{lang t="common|description"}</label></dt>
			<dd><input type="text" name="description" id="description" value="{$form.description}" maxlength="15"></dd>
		</dl>
		<dl>
			<dt><label for="picture">{lang t="emoticons|picture"}</label></dt>
			<dd><input type="file" name="picture" id="picture"></dd>
		</dl>
	</fieldset>
	<div class="form-bottom">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		<input type="reset" value="{lang t="common|reset"}" class="form">
		{$form_token}
	</div>
</form>