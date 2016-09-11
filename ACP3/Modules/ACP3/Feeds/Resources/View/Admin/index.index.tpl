{extends file="asset:System/layout.ajax-form.tpl"}

{block AJAX_FORM_CONTENT_BEFORE}
    {redirect_message}
{/block}
{block AJAX_FORM_CONTENT}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="feed_image" value=$form.feed_image maxlength=120 label={lang t="feeds|feed_image"}}
    {include file="asset:System/Partials/form_group.select.tpl" options=$feed_types required=true label={lang t="feeds|feed_type"}}
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/feeds"}}
{/block}
