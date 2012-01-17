<dl>
{if $seo.enable_uri_aliases === true}
	<dt>
		<label for="alias">{lang t="common|uri_alias"}</label>
		<span>{lang t="common|uri_alias_description"}</span>
	</dt>
	<dd><input type="text" name="form[alias]" id="alias" value="{$seo.alias}"></dd>
{/if}
	<dt>
		<label for="seo-keywords">{lang t="common|keywords"}</label>
		<span>({lang t="common|keywords_separate_with_commas"})</span>
	</dt>
	<dd><input type="text" name="form[seo_keywords]" id="seo-keywords" value="{$seo.keywords}"></dd>
	<dt><label for="seo-description">{lang t="common|description"}</label></dt>
	<dd><input type="text" name="form[seo_description]" id="seo-description" value="{$seo.description}"></dd>
</dl>