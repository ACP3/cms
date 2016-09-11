{extends file="asset:System/layout.ajax-form.tpl"}

{block AJAX_FORM_CONTENT}
    {include file="asset:System/Partials/form_group.select.tpl" options=$dateformat required=true label={lang t="system|date_format"}}
    {include file="asset:System/Partials/form_group.select.tpl" options=$sidebar_entries required=true label={lang t="system|sidebar_entries_to_display"}}
    {if isset($comments)}
        {include file="asset:System/Partials/form_group.button_group.tpl" options=$comments required=true label={lang t="system|allow_comments"}}
    {/if}
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/files"}}
{/block}
