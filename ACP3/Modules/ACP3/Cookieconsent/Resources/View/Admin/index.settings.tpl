{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_BEFORE_AJAX_FORM}
    {redirect_message}
{/block}
{block CONTENT_AJAX_FORM}
    {include file="asset:System/Partials/form_group.button_group.tpl" options=$enable required=true label={lang t="cookieconsent|enable_cookie_consent"}}
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token}
{/block}
