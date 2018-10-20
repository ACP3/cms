/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

/* global onloadCallback:true */
onloadCallback = function () {
    jQuery('.recaptcha-placeholder').each(function () {
        if (jQuery(this).children().length === 0) {
            const widgetId = grecaptcha.render(this.id, {
                'sitekey': this.dataset.sitekey,
                'size': this.dataset.size
            });

            jQuery(this).data('recaptchaId', widgetId);
        }
    });
};

jQuery(document).on('acp3.captcha.recaptcha', function () {
    onloadCallback();
});

jQuery(document).on('acp3.ajaxFrom.submit.fail', function (event, ajaxForm) {
    const $reCaptchaPlaceholder = jQuery(ajaxForm.element).find('.recaptcha-placeholder');

    if ($reCaptchaPlaceholder && $reCaptchaPlaceholder.length > 0) {
        grecaptcha.reset(
            $reCaptchaPlaceholder.data('recaptchaId')
        );
    }
});
