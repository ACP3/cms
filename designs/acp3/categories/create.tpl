{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
	<fieldset>
		<dl>
			<dt><label for="name">{lang t="common|name"}</label></dt>
			<dd><input type="text" name="name" id="name" value="{$form.name}" maxlength="120"></dd>
		</dl>
		<dl>
			<dt><label for="description">{lang t="common|description"}</label></dt>
			<dd><input type="text" name="description" id="description" value="{$form.description}" maxlength="120"></dd>
		</dl>
		<dl>
			<dt><label for="picture">{lang t="categories|picture"}</label></dt>
			<dd><input type="file" id="picture" name="picture" value=""></dd>
		</dl>
		<dl>
			<dt><label for="module">{lang t="categories|module"}</label></dt>
			<dd>
				<select name="module" id="module">
					<option value="">{lang t="common|pls_select"}</option>
{foreach $mod_list as $row}
					<option value="{$row.dir}"{$row.selected}>{$row.name}</option>
{/foreach}
				</select>
			</dd>
		</dl>
	</fieldset>
	<div class="form-bottom">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		<input type="reset" value="{lang t="common|reset"}" class="form">
		{$form_token}
	</div>
</form>