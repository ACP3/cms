/*
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

jQuery(document).ready(function ($) {
    $(':radio[name="sitemap_is_enabled"]').on('click change', function () {
        $('#seo-sitemap-wrapper').toggle(this.value == 1);
    }).filter(':checked').triggerHandler('click');
});
