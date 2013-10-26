{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="form-group">
		<label for="title" class="col-lg-2 control-label">{lang t="menus|menu_bar_title"}</label>
		<div class="col-lg-10"><input class="form-control" type="text" name="title" id="title" value="{$form.title}" maxlength="120" required></div>
	</div>
	<div class="form-group">
		<label for="index-name" class="col-lg-2 control-label">{lang t="menus|index_name"}</label>
		<div class="col-lg-10">
			<input class="form-control" type="text" name="index_name" id="index-name" value="{$form.index_name}" maxlength="20" required>
			<p class="help-block">{lang t="menus|index_name_description"}</p>
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-offset-2 col-lg-10">
			<button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
			<a href="{uri args="acp/menus"}" class="btn btn-default">{lang t="system|cancel"}</a>
			{$form_token}
		</div>
	</div>
</form>