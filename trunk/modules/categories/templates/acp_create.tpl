{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" enctype="multipart/form-data" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="title" class="control-label">{lang t="categories|title"}</label>
		<div class="controls"><input type="text" name="title" id="title" value="{$form.title}" maxlength="120" required></div>
	</div>
	<div class="control-group">
		<label for="description" class="control-label">{lang t="system|description"}</label>
		<div class="controls"><input type="text" name="description" id="description" value="{$form.description}" maxlength="120" required></div>
	</div>
	<div class="control-group">
		<label for="picture" class="control-label">{lang t="categories|picture"}</label>
		<div class="controls"><input type="file" id="picture" name="picture"></div>
	</div>
	<div class="control-group">
		<label for="module" class="control-label">{lang t="categories|module"}</label>
		<div class="controls">
			<select name="module" id="module">
{foreach $mod_list as $row}
				<option value="{$row.dir}"{$row.selected}>{$row.name}</option>
{/foreach}
			</select>
		</div>
	</div>
	<div class="form-actions">
		<button type="submit" name="submit" class="btn">{lang t="system|submit"}</button>
		<a href="{uri args="acp/categories"}" class="btn">{lang t="system|cancel"}</a>
		{$form_token}
	</div>
</form>