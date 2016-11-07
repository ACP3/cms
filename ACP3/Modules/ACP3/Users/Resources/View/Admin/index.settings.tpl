{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {include file="asset:System/Partials/form_group.input_email.tpl" name="mail" value=$form.mail required=true maxlength=120 label={lang t="system|email_address"}}
    {include file="asset:System/Partials/form_group.button_group.tpl" options=$languages required=true label={lang t="users|allow_language_override"}}
    {include file="asset:System/Partials/form_group.button_group.tpl" options=$registration required=true label={lang t="users|enable_registration"}}
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/users"}}
{/block}
