{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="width" class="control-label">{lang t="emoticons|image_width"}</label>
		<div class="controls">
			<input type="number" name="width" id="width" value="{$form.width}">
		<p class="help-block">{lang t="system|statements_in_pixel"}</p>
		</div>
	</div>
	<div class="control-group">
		<label for="height" class="control-label">{lang t="emoticons|image_height"}</label>
		<div class="controls">
			<input type="number" name="height" id="height" value="{$form.height}">
		<p class="help-block">{lang t="system|statements_in_pixel"}</p>
		</div>
	</div>
	<div class="control-group">
		<label for="filesize" class="control-label">{lang t="emoticons|image_filesize"}</label>
		<div class="controls">
			<input type="number" name="filesize" id="filesize" value="{$form.filesize}">
		<p class="help-block">{lang t="system|statements_in_byte"}</p>
		</div>
	</div>
	<div class="form-actions">
		<button type="submit" name="submit" class="btn">{lang t="system|submit"}</button>
		<a href="{uri args="acp/emoticons"}" class="btn">{lang t="system|cancel"}</a>
		{$form_token}
	</div>
</form>