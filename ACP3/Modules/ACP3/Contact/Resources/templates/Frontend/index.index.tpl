{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="name" value=$form.name required=true maxlength=80 readonly=$form.name_disabled label={lang t="system|name"}}
    {include file="asset:System/Partials/form_group.input_email.tpl" name="mail" value=$form.mail required=true readonly=$form.mail_disabled maxlength=120 label={lang t="system|email_address"}}
    {include file="asset:System/Partials/form_group.textarea.tpl" name="message" value=$form.message required=true label={lang t="system|message"}}
    {include file="asset:System/Partials/form_group.checkbox.tpl" options=$copy}
    {event name="captcha.event.display_captcha"}
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token}
{/block}
