/*
 * Copyright (c) 2017 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

jQuery(document).ready(function ($) {
    var $recaptchaWrapper = $('#recaptcha-wrapper');

    $('#captcha').on('change', function() {
        $recaptchaWrapper.toggle(this.value === 'captcha.extension.recaptcha_captcha_extension');
    }).filter(':selected').triggerHandler('change');
});
