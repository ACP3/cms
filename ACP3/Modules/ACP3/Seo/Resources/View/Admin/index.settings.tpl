{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="meta_description" value=$form.meta_description maxlength=120 label={lang t="seo|description"}}
    {include file="asset:System/Partials/form_group.textarea.tpl" name="meta_keywords" value=$form.meta_keywords label={lang t="seo|keywords"} help={lang t="seo|keywords_separate_with_commas"}}
    {include file="asset:System/Partials/form_group.select.tpl" options=$robots required=true label={lang t="seo|robots"}}
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/seo"}}
{/block}
