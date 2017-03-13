/*
 * Copyright (c) 2017 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

var onloadCallback = function() {
    jQuery('.recaptcha-placeholder').each(function() {
        grecaptcha.render(this.id, {
            'sitekey': this.dataset.sitekey,
            'size': this.dataset.size
        });
    });
};
