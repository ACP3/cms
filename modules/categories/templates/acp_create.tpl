{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" enctype="multipart/form-data" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="name" class="control-label">{lang t="common|name"}</label>
		<div class="controls"><input type="text" name="name" id="name" value="{$form.name}" maxlength="120"></div>
	</div>
	<div class="control-group">
		<label for="description" class="control-label">{lang t="common|description"}</label>
		<div class="controls"><input type="text" name="description" id="description" value="{$form.description}" maxlength="120"></div>
	</div>
	<div class="control-group">
		<label for="picture" class="control-label">{lang t="categories|picture"}</label>
		<div class="controls"><input type="file" id="picture" name="picture" value=""></div>
	</div>
	<div class="control-group">
		<label for="module" class="control-label">{lang t="categories|module"}</label>
		<div class="controls">
			<select name="module" id="module">
				<option value="">{lang t="common|pls_select"}</option>
{foreach $mod_list as $row}
				<option value="{$row.dir}"{$row.selected}>{$row.name}</option>
{/foreach}
			</select>
		</div>
	</div>
	<div class="form-actions">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="btn">
		<input type="reset" value="{lang t="common|reset"}" class="btn">
		{$form_token}
	</div>
</form>