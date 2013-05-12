{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" enctype="multipart/form-data" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="code" class="control-label">{lang t="emoticons|code"}</label>
		<div class="controls"><input type="text" name="code" id="code" value="{$form.code}" maxlength="10"></div>
	</div>
	<div class="control-group">
		<label for="description" class="control-label">{lang t="system|description"}</label>
		<div class="controls"><input type="text" name="description" id="description" value="{$form.description}" maxlength="15"></div>
	</div>
	<div class="control-group">
		<label for="picture" class="control-label">{lang t="emoticons|replace_picture"}</label>
		<div class="controls"><input type="file" name="picture" id="picture"></div>
	</div>
	<div class="form-actions">
		<button type="submit" name="submit" class="btn">{lang t="system|submit"}</button>
		<a href="{uri args="acp/emoticons"}" class="btn">{lang t="system|cancel"}</a>
		{$form_token}
	</div>
</form>