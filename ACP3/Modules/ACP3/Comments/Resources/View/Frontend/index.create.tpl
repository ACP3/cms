<br/>
{if isset($error_msg)}
    {$error_msg}
{/if}
<form action="{$REQUEST_URI}#comments" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
    {include file="asset:System/Partials/form_group.input_text.tpl" name="name" value=$form.name required=true maxlength=20 readonly=$form.name_disabled label={lang t="system|name"}}
    {if $can_use_emoticons}
        {$before_textarea={event name="emoticons.render_emoticons_list"}}
    {else}
        {$before_textarea=''}
    {/if}
    {include file="asset:System/Partials/form_group.textarea.tpl" name="message" value=$form.message required=true label={lang t="system|message"} before_textarea=$before_textarea}
    {event name="captcha.event.display_captcha"}
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token}
</form>
{javascripts}
    {include_js module="system" file="ajax-form"}
{/javascripts}
