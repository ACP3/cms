{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {include file="asset:System/Partials/form_group.select.tpl" options=$captchas required=true label={lang t="captcha|captcha_type"}}
    {event name="captcha.admin_settings.custom_fields" form=$form}
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/captcha"}}
{/block}
