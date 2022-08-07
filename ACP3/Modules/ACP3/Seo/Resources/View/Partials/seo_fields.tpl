<div class="row mb-3">
    <label for="alias" class="col-sm-2 col-form-label">
        {lang t="seo|alias"}
    </label>
    <div class="col-sm-10">
        {if !isset($disable_alias_suggest)}
            <div class="input-group">
                <input class="form-control" type="text" name="alias" id="alias" value="{$seo.alias}">
                <a href="{uri args="acp/seo/index/suggest"}"
                   id="seo-alias-suggestion"
                   class="btn btn-outline-secondary"
                   title="{lang t="seo|suggest_alias"}">
                    {icon iconSet="solid" icon="rotate"}
                    <span class="d-none d-sm-inline">{lang t="seo|suggest_alias"}</span>
                </a>
            </div>
        {else}
            <input class="form-control" type="text" name="alias" id="alias" value="{$seo.alias}">
        {/if}
        <p class="form-text">{lang t="seo|alias_description"}</p>
    </div>
</div>
{include file="asset:System/Partials/form_group.select.tpl" options=$seo.robots required=true label={lang t="seo|robots"}}
{include file="asset:System/Partials/form_group.input_text.tpl" name="seo_title" value=$seo.title maxlength=255 label={lang t="seo|title"} help={lang t="seo|title_help"}}
{include file="asset:System/Partials/form_group.input_text.tpl" name="seo_keywords" value=$seo.keywords maxlength=255 label={lang t="seo|keywords"}}
{include file="asset:System/Partials/form_group.input_text.tpl" name="seo_description" value=$seo.description maxlength=255 label={lang t="seo|description"}}
{include file="asset:System/Partials/form_group.input_text.tpl" name="seo_canonical" value=$seo.canonical maxlength=255 label={lang t="seo|canonical"}}
{include file="asset:System/Partials/form_group.textarea.tpl" name="seo_structured_data" value=$seo.structured_data label={lang t="seo|structured_data_json_ld"}}
{if !empty($seo.uri_pattern)}
    <input type="hidden" name="seo_uri_pattern" value="{$seo.uri_pattern}">
{/if}
{if !isset($disable_alias_suggest)}
    {javascripts}
        {include_js module="seo" file="partials/suggest_alias"}
    {/javascripts}
{/if}
