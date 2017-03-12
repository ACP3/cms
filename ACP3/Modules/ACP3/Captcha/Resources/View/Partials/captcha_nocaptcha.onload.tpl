{javascripts}
    <script type="text/javascript"
            src="https://www.google.com/recaptcha/api.js?hl={$recaptcha.lang}"></script>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $(document).on('acp3.ajaxFrom.submit.before', function(e, ajaxForm) {
                jQuery('.recaptcha-placeholder').each(function() {
                    console.log(this.id);
                    var widget = grecaptcha.render(this.id, {
                        'sitekey': '{$recaptcha.sitekey}',
                        'size': this.dataset.size,
                        'callback': function() {
                            ajaxForm.isFormValid = true;
                        }
                    });
                    grecaptcha.execute(widget);
                });
            });
        });
    </script>
{/javascripts}
