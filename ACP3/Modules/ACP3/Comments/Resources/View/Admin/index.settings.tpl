{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {include file="asset:System/Partials/form_group.select.tpl" options=$dateformat required=true label={lang t="system|date_format"}}
    {if isset($allow_emoticons)}
        {include file="asset:System/Partials/form_group.button_group.tpl" options=$allow_emoticons required=true label={lang t="comments|allow_emoticons"}}
    {/if}
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/comments"}}
{/block}
