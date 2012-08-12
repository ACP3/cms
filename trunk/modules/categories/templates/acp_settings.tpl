{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<fieldset>
		<legend>{lang t="categories|acp_settings"}</legend>
		<div class="control-group">
			<label for="width" class="control-label">{lang t="categories|image_width"}</label>
			<div class="controls">
				<input type="number" name="width" id="width" value="{$form.width}">
				<p class="help-block">{lang t="common|statements_in_pixel"}</p>
			</div>
		</div>
		<div class="control-group">
			<label for="height" class="control-label">{lang t="categories|image_height"}</label>
			<div class="controls">
				<input type="number" name="height" id="height" value="{$form.height}">
				<p class="help-block">{lang t="common|statements_in_pixel"}</p>
			</div>
		</div>
		<div class="control-group">
			<label for="filesize" class="control-label">{lang t="categories|image_filesize"}</label>
			<div class="controls">
				<input type="number" name="filesize" id="filesize" value="{$form.filesize}">
				<p class="help-block">{lang t="common|statements_in_byte"}</p>
			</div>
		</div>
	</fieldset>
	<div class="form-actions">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="btn">
		<input type="reset" value="{lang t="common|reset"}" class="btn">
		{$form_token}
	</div>
</form>