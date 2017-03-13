/*
 * Copyright (c) 2017 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

jQuery(document).ready(function ($) {
    var $recaptchaWrapper = $('#recaptcha-wrapper'),
        serviceIds = [
            'captcha.extension.recaptcha_captcha_extension'
        ];

    $('#captcha').on('change', function() {
        $recaptchaWrapper.toggle(serviceIds.indexOf(this.value) !== -1);
    }).filter(':selected').triggerHandler('change');
});
