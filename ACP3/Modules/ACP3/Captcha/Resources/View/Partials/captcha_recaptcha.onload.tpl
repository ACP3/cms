{javascripts}
    {include_js module="captcha" file="partials/recaptcha.onload"}
    <script type="text/javascript"
            src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&amp;render=explicit&amp;hl={$recaptcha.lang}"
            async
            defer></script>
{/javascripts}
