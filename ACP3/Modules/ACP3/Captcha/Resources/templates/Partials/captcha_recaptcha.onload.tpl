{javascripts}
    {include_js module="captcha" file="partials/recaptcha.onload"}
    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&amp;render=explicit&amp;hl={$recaptcha.lang}"
            async
            defer></script>
{/javascripts}
