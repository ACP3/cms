{if $seo.enable_uri_aliases === true}
<dl>
	<dt>
		<label for="alias">{lang t="common|seo_uri_alias"}</label>
		<span>{lang t="common|seo_uri_alias_description"}</span>
	</dt>
	<dd><input type="text" name="alias" id="alias" value="{$seo.alias}"></dd>
</dl>
{/if}
<dl>
	<dt>
		<label for="seo-keywords">{lang t="common|seo_keywords"}</label>
		<span>({lang t="common|seo_keywords_separate_with_commas"})</span>
	</dt>
	<dd><input type="text" name="seo_keywords" id="seo-keywords" value="{$seo.keywords}"></dd>
	<dt><label for="seo-description">{lang t="common|seo_description"}</label></dt>
	<dd><input type="text" name="seo_description" id="seo-description" value="{$seo.description}"></dd>
	<dt><label for="seo-robots">{lang t="common|seo_robots"}</label></dt>
	<dd>
		<select name="seo_robots" id="seo-robots">
{foreach $seo.robots as $row}
			<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
		</select>
	</dd>
</dl>