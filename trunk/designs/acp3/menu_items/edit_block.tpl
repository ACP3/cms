{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<fieldset>
		<dl>
			<dt>
				<label for="index_name">{lang t="menu_items|index_name"}</label>
				<span>({lang t="menu_items|index_name_description"})</span>
			</dt>
			<dd><input type="text" name="form[index_name]" id="index_name" value="{$form.index_name}" maxlength="20"></dd>
			<dt><label for="title">{lang t="menu_items|block_title"}</label></dt>
			<dd><input type="text" name="form[title]" id="title" value="{$form.title}" maxlength="120"></dd>
		</dl>
	</fieldset>
	<div class="form-bottom">
		<input type="submit" value="{lang t="common|submit"}" class="form">
		{$form_token}
	</div>
</form>