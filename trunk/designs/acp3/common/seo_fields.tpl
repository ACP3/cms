{if $seo.enable_uri_aliases === true}
<div class="control-group">
	<label for="alias" class="control-label">{lang t="common|seo_uri_alias"}</label>
	<div class="controls">
		<input type="text" name="alias" id="alias" value="{$seo.alias}">
		<p class="help-block">{lang t="common|seo_uri_alias_description"}</p>
	</div>
</div>
{/if}
<div class="control-group">
	<label for="seo-keywords" class="control-label">{lang t="common|seo_keywords"}</label>
	<div class="controls">
		<input type="text" name="seo_keywords" id="seo-keywords" value="{$seo.keywords}">
		<p class="help-block">{lang t="common|seo_keywords_separate_with_commas"}</p>
	</div>
</div>
<div class="control-group">
	<label for="seo-description" class="control-label">{lang t="common|seo_description"}</label>
	<div class="controls">
		<input type="text" name="seo_description" id="seo-description" value="{$seo.description}">
	</div>
</div>
<div class="control-group">
	<label for="seo-robots" class="control-label">{lang t="common|seo_robots"}</label>
	<div class="controls">
		<select name="seo_robots" id="seo-robots">
{foreach $seo.robots as $row}
			<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
		</select>
	</div>
</div>