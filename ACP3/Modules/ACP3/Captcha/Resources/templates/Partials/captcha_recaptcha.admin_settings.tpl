<div id="recaptcha-wrapper">
    {include file="asset:System/Partials/form_group.input_password.tpl" name="recaptcha_sitekey" value=$form.recaptcha_sitekey labelRequired=true label={lang t="captcha|recaptcha_sitekey"}}
    {include file="asset:System/Partials/form_group.input_password.tpl" name="recaptcha_secret" value=$form.recaptcha_secret labelRequired=true label={lang t="captcha|recaptcha_secret"}}
</div>
{javascripts}
    {include_js module="captcha" file="partials/recaptcha.admin-settings"}
{/javascripts}
