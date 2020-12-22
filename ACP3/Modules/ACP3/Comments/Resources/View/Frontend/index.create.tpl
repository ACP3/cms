{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_BEFORE_AJAX_FORM prepend}<br>{/block}

{block CONTENT_AJAX_FORM_URI}{$REQUEST_URI}#comments{/block}

{block CONTENT_AJAX_FORM}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="name" value=$form.name required=true maxlength=20 readonly=$form.name_disabled label={lang t="system|name"}}
    {include file="asset:System/Partials/form_group.wysiwyg.tpl" name="message" value=$form.message required=true label={lang t="system|message"} editor="core.wysiwyg.textarea"}
    {event name="captcha.event.display_captcha"}
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token}
{/block}
