{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<fieldset>
		<legend>{lang t="menu_items|acp_create_block"}</legend>
		<div class="control-group">
			<label for="index-name" class="control-label">{lang t="menu_items|index_name"}</label>
			<div class="controls">
				<input type="text" name="index_name" id="index-name" value="{$form.index_name}" maxlength="20">
				<p class="help-block">{lang t="menu_items|index_name_description"}</p>
			</div>
		</div>
		<div class="control-group">
			<label for="title" class="control-label">{lang t="menu_items|block_title"}</label>
			<div class="controls"><input type="text" name="title" id="title" value="{$form.title}" maxlength="120"></div>
		</div>
	</fieldset>
	<div class="form-actions">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="btn">
		<input type="reset" value="{lang t="common|reset"}" class="btn">
		{$form_token}
	</div>
</form>