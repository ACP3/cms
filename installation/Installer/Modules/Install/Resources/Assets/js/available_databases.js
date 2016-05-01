/*
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */
jQuery(document).ready(function ($) {
    var ajaxUrl = $('#config-form').data('available-databases-url'),
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
                $dbName.prop('disabled', false);

                if (response.length > 0) {
                    for (var i = 0; i < response.length; ++i) {
                        $dbName.append('<option value="' + response[i] + '">' + response[i] + '</option>');
                    }

                    $formGroup.addClass('has-success');
                } else {
                    $formGroup.addClass('has-error');
                }
            }
        );
    });
});
