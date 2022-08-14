{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {include file="asset:System/Partials/form_group.input_email.tpl" name="mail" value=$form.mail required=true maxlength=120 label={lang t="system|email_address"}}
    {include file="asset:System/Partials/form_group.select.tpl" options=$salutation columnSelector="col-md-3" label={lang t="newsletter|salutation"}}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="first_name" value=$form.first_name maxlength=120 label={lang t="newsletter|first_name"}}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="last_name" value=$form.last_name maxlength=120 label={lang t="newsletter|last_name"}}
    {event name="captcha.event.display_captcha"}
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_btn_class="btn-link" back_url={uri args="newsletter/archive/index"} back_label={lang t="newsletter|missed_out_newsletter"}}
{/block}
