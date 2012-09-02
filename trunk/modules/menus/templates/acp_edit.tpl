{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="title" class="control-label">{lang t="menus|menu_bar_title"}</label>
		<div class="controls"><input type="text" name="title" id="title" value="{$form.title}" maxlength="120"></div>
	</div>
	<div class="control-group">
		<label for="index-name" class="control-label">{lang t="menus|index_name"}</label>
		<div class="controls">
			<input type="text" name="index_name" id="index-name" value="{$form.index_name}" maxlength="20">
			<p class="help-block">{lang t="menus|index_name_description"}</p>
		</div>
	</div>
	<div class="form-actions">
		<button type="submit" name="submit" class="btn">{lang t="common|submit"}</button>
		<a href="{uri args="acp/menus"}" class="btn">{lang t="common|cancel"}</a>
		{$form_token}
	</div>
</form>