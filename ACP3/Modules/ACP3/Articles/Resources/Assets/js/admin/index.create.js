/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

(($) => {
    $('input[name="active"]').on('click change', function () {
        $('#publication-period-wrapper').toggle(this.value === '1');
    }).filter(':checked').click();
})(jQuery);
