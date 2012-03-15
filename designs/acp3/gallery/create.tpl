{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<div id="tabs">
		<ul>
			<li><a href="#tab-1">{lang t="common|general_statements"}</a></li>
			<li><a href="#tab-2">{lang t="common|seo"}</a></li>
		</ul>
		<div id="tab-1">
			{$publication_period}
			<dl>
				<dt><label for="name">{lang t="gallery|title"}</label></dt>
				<dd><input type="text" name="name" id="name" value="{$form.name}" maxlength="120"></dd>
			</dl>
		</div>
		<div id="tab-2">
			{$SEO_FORM_FIELDS}
		</div>
	</div>
	<div class="form-bottom">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		<input type="reset" value="{lang t="common|reset"}" class="form">
		{$form_token}
	</div>
</form>