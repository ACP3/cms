{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {include file="asset:System/Partials/form_group.select.tpl" options=$dateformat required=true label={lang t="system|date_format"}}
    {include file="asset:System/Partials/form_group.select.tpl" options=$notify required=true label={lang t="guestbook|notification"}}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="notify_email" value=$form.notify_email labelRequired=true label={lang t="guestbook|notification_email"}}
    {include file="asset:System/Partials/form_group.button_group.tpl" options=$overlay required=true label={lang t="guestbook|use_overlay"}}
    {if isset($options.allow_emoticons)}
        {include file="asset:System/Partials/form_group.button_group.tpl" options=$options.allow_emoticons required=true label={lang t="guestbook|allow_emoticons"}}
    {/if}
    {if isset($options.newsletter_integration)}
        {include file="asset:System/Partials/form_group.button_group.tpl" options=$options.newsletter_integration required=true label={lang t="guestbook|newsletter_integration"}}
    {/if}
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/guestbook"}}
    {javascripts}
        {include_js module="guestbook" file="admin/index.settings"}
    {/javascripts}
{/block}
