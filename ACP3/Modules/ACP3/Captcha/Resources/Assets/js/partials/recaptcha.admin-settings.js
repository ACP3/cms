/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

jQuery(document).ready(($) => {
    const $recaptchaWrapper = $('#recaptcha-wrapper'),
        serviceIds = [
            'captcha.extension.recaptcha_captcha_extension'
        ];

    $(':input[name="captcha"]').on('change', function () {
        $recaptchaWrapper.toggle(serviceIds.indexOf(this.value) !== -1);
    }).triggerHandler('change');
});
