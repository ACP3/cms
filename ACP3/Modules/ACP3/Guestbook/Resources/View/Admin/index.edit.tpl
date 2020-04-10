{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {include file="asset:System/Partials/form_group.wysiwyg.tpl" name="message" value=$form.message required=true label={lang t="system|message"} editor="core.wysiwyg.textarea"}
    {if !empty($activate)}
        {include file="asset:System/Partials/form_group.button_group.tpl" options=$activate required=true label={lang t="guestbook|activate_entry"}}
    {/if}
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/guestbook"}}
{/block}
