{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<fieldset>
		<dl>
			<dt><label for="width">{lang t="categories|image_width"}</label></dt>
			<dd><input type="number" name="form[width]" id="width" value="{$form.width}"></dd>
			<dt><label for="height">{lang t="categories|image_height"}</label></dt>
			<dd><input type="number" name="form[height]" id="height" value="{$form.height}"></dd>
			<dt><label for="filesize">{lang t="categories|image_filesize"}</label></dt>
			<dd><input type="number" name="form[filesize]" id="filesize" value="{$form.filesize}"></dd>
		</dl>
	</fieldset>
	<div class="form-bottom">
		<input type="submit" value="{lang t="common|submit"}" class="form">
		<input type="reset" value="{lang t="common|reset"}" class="form">
		{$form_token}
	</div>
</form>