{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_BEFORE_AJAX_FORM}
    {redirect_message}
{/block}
{block CONTENT_AJAX_FORM}
    {include file="asset:System/Partials/form_group.button_group.tpl" options=$enable required=true label={lang t="cookieconsent|enable_cookie_consent"}}
    <div id="cookie-consent-container">
        {include file="asset:System/Partials/form_group.select.tpl" options=$type required=true label={lang t="cookieconsent|cookie_consent_type"}}
        {include file="asset:System/Partials/form_group.wysiwyg.tpl" name="text" value=$form.text toolbar="simple" label={lang t="cookieconsent|cookie_consent_text"}}
    </div>
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token}
    {javascripts}
        {include_js module="cookieconsent" file="admin/index.settings"}
    {/javascripts}
{/block}
