{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<div id="tabs">
		<ul>
			<li><a href="#tab-1">{lang t="common|publication_period"}</a></li>
			<li><a href="#tab-2">{lang t="static_pages|page_statements"}</a></li>
			<li><a href="#tab-3">{lang t="common|seo"}</a></li>
		</ul>
		<div id="tab-1">
			<dl>
				<dt><label for="start">{lang t="common|publication_period"}</label></dt>
				<dd>{$publication_period}</dd>
			</dl>
			<p>
				{lang t="common|date_description"}
			</p>
		</div>
		<div id="tab-2" class="ui-tabs-hide">
			<dl>
				<dt><label for="title">{lang t="static_pages|title"}</label></dt>
				<dd><input type="text" name="form[title]" id="title" value="{$form.title}" maxlength="120"></dd>
				<dt><label for="text">{lang t="static_pages|text"}</label></dt>
				<dd>{wysiwyg name="text" value="`$form.text`" height="250"}</dd>
			</dl>
		</div>
		<div id="tab-3" class="ui-tabs-hide">
			{seo_fields alias="`$form.alias`" keywords="`$form.seo_keywords`" description="`$form.seo_description`"}
		</div>
	</div>
	<div class="form-bottom">
		<input type="submit" value="{lang t="common|submit"}" class="form">
		<input type="reset" value="{lang t="common|reset"}" class="form">
		{$form_token}
	</div>
</form>