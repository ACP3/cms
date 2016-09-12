jQuery(document).ready(function ($) {
    $(':checkbox[name="create"]').on('click', function () {
        $('#create-menu-item-container').toggle($(this).is(':checked'));
    }).triggerHandler('click');
});
