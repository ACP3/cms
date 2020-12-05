/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

(($) => {
    $(':radio[name="sitemap_is_enabled"]').on('click change', function () {
        $('#seo-sitemap-wrapper').toggle(parseInt(this.value) === 1);
    }).filter(':checked').triggerHandler('click');
})(jQuery);
