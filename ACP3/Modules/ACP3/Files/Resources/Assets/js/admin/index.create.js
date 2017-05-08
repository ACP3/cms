jQuery(document).ready(function ($) {
    $('input[name="active"]').on('click change', function () {
        $('#publication-period-wrapper').toggle(this.value === '1');
    }).filter(':checked').click();

    $(':checkbox[name="external"]')
        .on('click', function () {
            $('#file-external-toggle').toggle($(this).is(':checked'));
            $('#file-internal-toggle').toggle(!$(this).is(':checked'));
        })
        .triggerHandler('click');
});
