/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

jQuery(document).ready(function ($) {
    $('#modal-create').on('show.bs.modal', function (event) {
        const $target = $(event.relatedTarget);
        const $modal = $(this);

        $.ajax($target.attr('href'))
            .done((responseData) => {
                $modal.find('.modal-content').html(responseData);
                $modal.find('[data-ajax-form="true"]').formSubmit();
            });
    });
});
