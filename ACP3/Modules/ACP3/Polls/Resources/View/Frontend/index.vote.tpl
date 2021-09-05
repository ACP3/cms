{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {if $multiple == '1'}
        {include file="asset:System/Partials/form_group.checkbox.tpl" options=$answers label=$question}
    {else}
        {include file="asset:System/Partials/form_group.radio.tpl" options=$answers label=$question}
    {/if}
    {include file="asset:System/Partials/form_group.submit.tpl"}
{/block}
