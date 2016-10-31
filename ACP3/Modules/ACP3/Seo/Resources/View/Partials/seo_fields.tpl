<div class="form-group">
    <label for="alias" class="col-sm-2 control-label">
        {lang t="seo|alias"}
    </label>
    <div class="col-sm-10">
        {if !isset($disable_alias_suggest)}
            <div class="input-group">
                <input class="form-control" type="text" name="alias" id="alias" value="{$seo.alias}">
                <span class="input-group-btn">
                    <a href="{uri args="acp/seo/index/suggest"}"
                       id="seo-alias-suggestion"
                       class="btn btn-default"
                       title="{lang t="seo|suggest_alias"}">
                        <i class="glyphicon glyphicon-refresh"></i>
                        <span class="hidden-xs">{lang t="seo|suggest_alias"}</span>
                    </a>
                </span>
            </div>
        {else}
            <input class="form-control" type="text" name="alias" id="alias" value="{$seo.alias}">
        {/if}
        <p class="help-block">{lang t="seo|alias_description"}</p>
    </div>
</div>
{include file="asset:System/Partials/form_group.input_text.tpl" name="seo_keywords" value=$seo.keywords maxlength=255 label={lang t="seo|keywords"}}
{include file="asset:System/Partials/form_group.input_text.tpl" name="seo_description" value=$seo.description maxlength=255 label={lang t="seo|description"}}
{include file="asset:System/Partials/form_group.select.tpl" options=$seo.robots required=true label={lang t="seo|robots"}}
{if !empty($seo.uri_pattern)}
    <input type="hidden" name="seo_uri_pattern" value="{$seo.uri_pattern}">
{/if}
{if !isset($disable_alias_suggest)}
    {javascripts}
        {include_js module="seo" file="suggest_alias"}
    {/javascripts}
{/if}
