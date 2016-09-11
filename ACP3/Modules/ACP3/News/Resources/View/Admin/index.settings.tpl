{extends file="asset:System/ajax-form.tpl"}

{block AJAX_FORM_CONTENT}
    {include file="asset:System/Partials/form_group.select.tpl" options=$dateformat required=true label={lang t="system|date_format"}}
    {include file="asset:System/Partials/form_group.select.tpl" options=$sidebar_entries required=true label={lang t="system|sidebar_entries_to_display"}}
    {include file="asset:System/Partials/form_group.button_group.tpl" options=$readmore required=true label={lang t="news|activate_readmore"}}
    {include file="asset:System/Partials/form_group.input_number.tpl" name="readmore_chars" value=$form.readmore_chars required=true label={lang t="news|readmore_chars"}}
    {include file="asset:System/Partials/form_group.button_group.tpl" options=$category_in_breadcrumb required=true label={lang t="news|display_category_in_breadcrumb"}}
    {if isset($allow_comments)}
        {include file="asset:System/Partials/form_group.button_group.tpl" options=$allow_comments required=true label={lang t="system|allow_comments"}}
    {/if}
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/news"}}
    {javascripts}
        {include_js module="news" file="admin/index.settings"}
    {/javascripts}
{/block}
