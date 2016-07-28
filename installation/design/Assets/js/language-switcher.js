/*
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

jQuery(document).ready(function ($) {
    var $doc = $(document);
    $doc.data('has-changes', false);

    $('input, select').change(function () {
        if ($(this).attr('id') !== 'lang') {
            $doc.data('has-changes', true);
        }
    });

    // Sprachdropdown
    $('#languages').find(':submit').hide();
    $('#lang').change(function () {
        var allowPageReload = true;
        if ($doc.length > 0 && $doc.data('has-changes') == true) {
            allowPageReload = confirm($('#lang').data('change-language-warning'));
        }

        if (allowPageReload === true) {
            $('#languages').submit();
        }
    });
});
