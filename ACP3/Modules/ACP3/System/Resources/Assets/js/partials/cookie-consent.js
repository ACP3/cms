/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

jQuery(document).ready(($) => {
    const $cookieNotice = $('#cookie-consent'),
        cookieName = 'ACCEPT_COOKIES';

    $('#accept-cookies').click(function () {
        Cookies.set(cookieName, true, {expires: 365});

        $cookieNotice.fadeOut();
    });

    if (!Cookies.get(cookieName)) {
        $cookieNotice.removeClass('d-none');
    }
});
