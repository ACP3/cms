/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

var onloadCallback = function() {
    jQuery('.recaptcha-placeholder').each(function() {
        this.innerHtml = '';

        grecaptcha.render(this.id, {
            'sitekey': this.dataset.sitekey,
            'size': this.dataset.size
        });
    });
};
