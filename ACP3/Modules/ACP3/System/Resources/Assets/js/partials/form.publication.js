/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

(($, document) => {
    $('input[name="active"]').on('click change', function () {
        document.getElementById('publication-period-wrapper').classList.toggle('hidden',Number(this.value) === 0);
    }).filter(':checked').click();
})(jQuery, document);
