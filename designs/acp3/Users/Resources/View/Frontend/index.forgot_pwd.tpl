{$LAYOUT="System/layout.simple.tpl"}

{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {$floatingLabel=true}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="nick_mail" value=$form.nick_mail required=true maxlength=120 label={lang t="users|nickname_or_email"}}
    {event name="captcha.event.display_captcha" input_only=true}
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="users/index/login"} back_label={lang t="users|to_login"} back_btn_class='btn-link'}
{/block}
