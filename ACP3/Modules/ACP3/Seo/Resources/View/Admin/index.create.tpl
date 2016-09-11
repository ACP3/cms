{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="uri" value=$form.uri required=true maxlength=120 label={lang t="seo|uri"}}
    {include file="asset:Seo/Partials/seo_fields.tpl" seo=$SEO_FORM_FIELDS disable_alias_suggest=true}
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/seo"}}
{/block}
