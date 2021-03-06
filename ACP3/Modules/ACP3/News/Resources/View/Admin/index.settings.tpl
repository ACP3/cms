{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {include file="asset:System/Partials/form_group.select.tpl" options=$dateformat required=true label={lang t="system|date_format"}}
    {include file="asset:System/Partials/form_group.select.tpl" options=$sidebar_entries required=true label={lang t="system|sidebar_entries_to_display"}}
    {include file="asset:System/Partials/form_group.button_group.tpl" options=$readmore required=true label={lang t="news|activate_readmore"}}
    {include file="asset:System/Partials/form_group.input_number.tpl" formGroupId="readmore-characters-wrapper" name="readmore_chars" value=$readmore_chars required=true label={lang t="news|readmore_chars"}}
    {include file="asset:System/Partials/form_group.button_group.tpl" options=$category_in_breadcrumb required=true label={lang t="news|display_category_in_breadcrumb"}}
    {event name="news.layout.settings"}
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/news"}}
    {javascripts}
        {include_js module="news" file="admin/index.settings"}
    {/javascripts}
{/block}
