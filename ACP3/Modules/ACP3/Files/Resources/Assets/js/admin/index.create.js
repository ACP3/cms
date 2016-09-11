jQuery(document).ready(function ($) {
    $(':checkbox[name="external"]')
        .on('click change', function () {
            $('#file-external-toggle').toggle($(this).is(':checked'));
            $('#file-internal-toggle').toggle(!$(this).is(':checked'));
        })
        .triggerHandler('click');
});
