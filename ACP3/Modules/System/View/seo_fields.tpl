<div class="form-group">
    <label for="alias" class="col-lg-2 control-label">{lang t="system|seo_uri_alias"}</label>

    <div class="col-lg-10">
        <input class="form-control" type="text" name="alias" id="alias" value="{$seo.alias}">

        <p class="help-block">{lang t="system|seo_uri_alias_description"}</p>
    </div>
</div>
<div class="form-group">
    <label for="seo-keywords" class="col-lg-2 control-label">{lang t="system|seo_keywords"}</label>

    <div class="col-lg-10">
        <input class="form-control" type="text" name="seo_keywords" id="seo-keywords" value="{$seo.keywords}">

        <p class="help-block">{lang t="system|seo_keywords_separate_with_commas"}</p>
    </div>
</div>
<div class="form-group">
    <label for="seo-description" class="col-lg-2 control-label">{lang t="system|seo_description"}</label>

    <div class="col-lg-10">
        <input class="form-control" type="text" name="seo_description" id="seo-description" value="{$seo.description}">
    </div>
</div>
<div class="form-group">
    <label for="seo-robots" class="col-lg-2 control-label">{lang t="system|seo_robots"}</label>

    <div class="col-lg-10">
        <select class="form-control" name="seo_robots" id="seo-robots">
            {foreach $seo.robots as $row}
                <option value="{$row.value}"{$row.selected}>{$row.lang}</option>
            {/foreach}
        </select>
    </div>
</div>