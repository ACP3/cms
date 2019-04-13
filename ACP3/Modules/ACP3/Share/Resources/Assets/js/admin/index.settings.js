/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

jQuery(document).ready(($) => {
    $('#services').on('change', function () {
        $('#fb-credentials-wrapper').toggle($(this).val() && $(this).val().indexOf('facebook') !== -1);
    }).triggerHandler('change');
});
