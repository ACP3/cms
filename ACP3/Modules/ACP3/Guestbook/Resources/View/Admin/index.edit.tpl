{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {if $can_use_emoticons}
        {$before_textarea={event name="emoticons.render_emoticons_list"}}
    {else}
        {$before_textarea=''}
    {/if}
    {include file="asset:System/Partials/form_group.textarea.tpl" name="message" value=$form.message required=true label={lang t="system|message"} before_textarea=$before_textarea}
    {if !empty($activate)}
        {include file="asset:System/Partials/form_group.button_group.tpl" options=$activate required=true label={lang t="guestbook|activate_entry"}}
    {/if}
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/guestbook"}}
{/block}
