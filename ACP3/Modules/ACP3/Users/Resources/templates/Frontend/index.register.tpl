{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="nickname" value=$form.nickname required=true maxlength=30 label={lang t="users|nickname"}}
    {include file="asset:System/Partials/form_group.input_email.tpl" name="mail" value=$form.mail required=true maxlength=120 label={lang t="system|email_address"}}
    {include file="asset:Users/Partials/password_fields.tpl" required=true}
    {event name="captcha.event.display_captcha"}
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token}
{/block}
