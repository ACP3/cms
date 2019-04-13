/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */
jQuery(document).ready(($) => {
    const ajaxUrl = $('#config-form').data('available-databases-url'),
        $dbName = $('#db-name'),
        $formFields = $('#db-host, #db-user, #db-password');

    $formFields.change(function () {
        $dbName
            .prop('disabled', true)
            .find('option').not(':first').remove();

        $formFields.removeClass('is-invalid');
        $formFields.removeClass('is-valid');

        $.post(
            ajaxUrl,
            {
                'db_host': $('#db-host').val(),
                'db_user': $('#db-user').val(),
                'db_password': $('#db-password').val()
            },
            function (response) {
                if (response.length === 0) {
                    $formFields.addClass('is-invalid');
                    return;
                }

                for (let i = 0; i < response.length; ++i) {
                    $dbName.append('<option value="' + response[i] + '">' + response[i] + '</option>');
                }

                $formFields.addClass('is-valid');
            }
        ).always(function () {
            $dbName.prop('disabled', false);
        });
    });
});
