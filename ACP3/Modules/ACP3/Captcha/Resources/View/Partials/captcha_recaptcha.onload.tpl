{javascripts}
    <script type="text/javascript">
        var onloadCallback = function() {
            jQuery('.recaptcha-placeholder').each(function() {
                grecaptcha.render(this.id, {
                    'sitekey': "{$recaptcha.sitekey}"
                });
            });
        };
    </script>
    <script type="text/javascript"
            src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&amp;render=explicit&amp;hl={$recaptcha.lang}"
            async
            defer></script>
{/javascripts}
