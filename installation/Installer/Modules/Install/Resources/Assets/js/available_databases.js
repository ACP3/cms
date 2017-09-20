/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */
jQuery(document).ready(function ($) {
    const ajaxUrl = $('#config-form').data('available-databases-url'),
        $dbName = $('#db-name'),
        $formFields = $('#db-host, #db-user, #db-password'),
        $formGroup = $formFields.closest('.form-group');

    $formFields.change(function () {
        $dbName
            .prop('disabled', true)
            .find('option').not(':first').remove();
        $formGroup
            .removeClass('has-success')
            .removeClass('has-error');

        $.post(
            ajaxUrl,
            {
                "db_host": $('#db-host').val(),
                "db_user": $('#db-user').val(),
                "db_password": $('#db-password').val()
            },
            function (response) {
                if (response.length > 0) {
                    for (const dbName of response) {
                        $dbName.append('<option value="' + dbName + '">' + dbName + '</option>');
                    }

                    $formGroup.addClass('has-success');
                } else {
                    $formGroup.addClass('has-error');
                }
            }
        ).always(function() {
            $dbName.prop('disabled', false);
        });
    });
});
