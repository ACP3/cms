/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

/* global onloadCallback:true */
onloadCallback = function() {
    jQuery('.recaptcha-placeholder').each((index, element) => {
        element.innerHtml = '';

        grecaptcha.render(element.id, {
            'sitekey': element.dataset.sitekey,
            'size': element.dataset.size
        });
    });
};
