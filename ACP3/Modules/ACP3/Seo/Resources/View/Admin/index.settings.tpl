{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
<div class="tabbable">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|general"}</a></li>
        <li><a href="#tab-2" data-toggle="tab">{lang t="seo|sitemap"}</a></li>
    </ul>
    <div class="tab-content">
        <div id="tab-1" class="tab-pane fade in active">
            {include file="asset:System/Partials/form_group.input_text.tpl" name="meta_description" value=$form.meta_description maxlength=120 label={lang t="seo|description"}}
            {include file="asset:System/Partials/form_group.textarea.tpl" name="meta_keywords" value=$form.meta_keywords label={lang t="seo|keywords"} help={lang t="seo|keywords_separate_with_commas"}}
            {include file="asset:System/Partials/form_group.select.tpl" options=$robots required=true label={lang t="seo|robots"}}
        </div>
        <div id="tab-2" class="tab-pane fade">
            {include file="asset:System/Partials/form_group.button_group.tpl" options=$sitemap_is_enabled required=true label={lang t="seo|sitemap_enable"}}
            {include file="asset:System/Partials/form_group.button_group.tpl" options=$sitemap_save_mode required=true label={lang t="seo|sitemap_save_mode"}}
        </div>
    </div>
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/seo"}}
{/block}
