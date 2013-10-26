{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" enctype="multipart/form-data" accept-charset="UTF-8" class="form-horizontal">
	<div class="form-group">
		<label for="code" class="col-lg-2 control-label">{lang t="emoticons|code"}</label>
		<div class="col-lg-10"><input class="form-control" type="text" name="code" id="code" value="{$form.code}" maxlength="10"></div>
	</div>
	<div class="form-group">
		<label for="description" class="col-lg-2 control-label">{lang t="system|description"}</label>
		<div class="col-lg-10"><input class="form-control" type="text" name="description" id="description" value="{$form.description}" maxlength="15"></div>
	</div>
	<div class="form-group">
		<label for="picture" class="col-lg-2 control-label">{lang t="emoticons|replace_picture"}</label>
		<div class="col-lg-10"><input type="file" name="picture" id="picture"></div>
	</div>
	<div class="form-group">
		<div class="col-lg-offset-2 col-lg-10">
			<button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
			<a href="{uri args="acp/emoticons"}" class="btn btn-default">{lang t="system|cancel"}</a>
			{$form_token}
		</div>
	</div>
</form>