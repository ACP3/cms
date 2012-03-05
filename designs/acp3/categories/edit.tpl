{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
	<fieldset>
		<dl>
			<dt><label for="name">{lang t="common|name"}</label></dt>
			<dd><input type="text" name="name" id="name" value="{$form.name}" maxlength="120"></dd>
		</dl>
		<dl>
			<dt><label for="description">{lang t="common|description"}</label></dt>
			<dd><input type="text" name="description" id="description" value="{$form.description}" maxlength="120"></dd>
		</dl>
		<dl>
			<dt><label for="picture">{lang t="categories|picture"}</label></dt>
			<dd><input type="file" id="picture" name="picture" value=""></dd>
		</dl>
	</fieldset>
	<div class="form-bottom">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		{$form_token}
	</div>
</form>