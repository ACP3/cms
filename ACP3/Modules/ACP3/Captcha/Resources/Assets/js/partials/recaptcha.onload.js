/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

let onloadCallback = function() {
    jQuery('.recaptcha-placeholder').each((index, element) => {
        element.innerHtml = '';

        grecaptcha.render(this.id, {
            'sitekey': this.dataset.sitekey,
            'size': this.dataset.size
        });
    });
};
