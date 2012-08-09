{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<fieldset>
		<legend>{lang t="menu_items|acp_create_block"}</legend>
		<dl>
			<dt>
				<label for="index-name">{lang t="menu_items|index_name"}</label>
				<span>({lang t="menu_items|index_name_description"})</span>
			</dt>
			<dd><input type="text" name="index_name" id="index-name" value="{$form.index_name}" maxlength="20"></dd>
		</dl>
		<dl>
			<dt><label for="title">{lang t="menu_items|block_title"}</label></dt>
			<dd><input type="text" name="title" id="title" value="{$form.title}" maxlength="120"></dd>
		</dl>
	</fieldset>
	<div class="form-bottom">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		<input type="reset" value="{lang t="common|reset"}" class="form">
		{$form_token}
	</div>
</form>