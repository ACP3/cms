<div class="form-group">
    <label for="alias" class="col-sm-2 control-label">
        {lang t="seo|alias"}
    </label>

    <div class="col-sm-10">
        {if !isset($disable_alias_suggest)}
            <div class="input-group">
                <input class="form-control" type="text" name="alias" id="alias" value="{$seo.alias}">
                <span class="input-group-btn">
                    <a href="{uri args="acp/seo/index/suggest"}" id="seo-alias-suggestion" class="btn btn-default">
                        <i class="glyphicon glyphicon-refresh"></i>
                        {lang t="seo|suggest_alias"}
                    </a>
                </span>
            </div>
        {else}
            <input class="form-control" type="text" name="alias" id="alias" value="{$seo.alias}">
        {/if}

        <p class="help-block">{lang t="seo|alias_description"}</p>
    </div>
</div>
<div class="form-group">
    <label for="seo-keywords" class="col-sm-2 control-label">{lang t="seo|keywords"}</label>

    <div class="col-sm-10">
        <input class="form-control" type="text" name="seo_keywords" id="seo-keywords" value="{$seo.keywords}">

        <p class="help-block">{lang t="seo|keywords_separate_with_commas"}</p>
    </div>
</div>
<div class="form-group">
    <label for="seo-description" class="col-sm-2 control-label">{lang t="seo|description"}</label>

    <div class="col-sm-10">
        <input class="form-control" type="text" name="seo_description" id="seo-description" value="{$seo.description}">
    </div>
</div>
<div class="form-group">
    <label for="seo-robots" class="col-sm-2 control-label">{lang t="seo|robots"}</label>

    <div class="col-sm-10">
        <select class="form-control" name="seo_robots" id="seo-robots" required>
            {foreach $seo.robots as $row}
                <option value="{$row.value}"{$row.selected}>{$row.lang}</option>
            {/foreach}
        </select>
    </div>
</div>
{if !isset($disable_alias_suggest)}
    {javascripts}
        {include_js module="seo" file="suggest_alias"}
    {/javascripts}
{/if}
