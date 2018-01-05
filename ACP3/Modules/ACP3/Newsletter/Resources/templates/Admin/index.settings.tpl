{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {include file="asset:System/Partials/form_group.input_email.tpl" name="mail" value=$form.mail required=true maxlength=120 label={lang t="system|email_address"}}
    {include file="asset:System/Partials/form_group.wysiwyg.tpl" name="mailsig" value=$form.mailsig label={lang t="newsletter|mailsig"}}
    {include file="asset:System/Partials/form_group.button_group.tpl" options=$html required=true label={lang t="newsletter|send_html_emails"}}
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/newsletter"}}
{/block}
