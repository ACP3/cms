/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

(($) => {
    $(':radio[name="share_active"]').on('click change', function () {
        $('#share-services-wrapper').toggle(parseInt(this.value) === 1);
    }).filter(':checked').triggerHandler('click');

    $(':radio[name="share_customize_services"]').on('click change', function () {
        $('#share-custom-services-wrapper').toggle(parseInt(this.value) === 1);
    }).filter(':checked').triggerHandler('click');
})(jQuery);
