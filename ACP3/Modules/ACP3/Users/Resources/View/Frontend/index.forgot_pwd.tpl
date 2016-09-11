{extends file="asset:System/layout.ajax-form.tpl"}

{block AJAX_FORM_CONTENT}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="nick_mail" value=$form.nick_mail required=true maxlength=120 label={lang t="users|nickname_or_email"}}
    {event name="captcha.event.display_captcha"}
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token}
{/block}
