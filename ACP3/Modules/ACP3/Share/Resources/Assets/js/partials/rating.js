/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

jQuery(document).ready(function($) {
    $('#rating-wrapper').on('change', ':radio', function() {
        $(this).closest('form').submit();
    });
});
