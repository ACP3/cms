jQuery(document).ready(function ($) {
    $(':checkbox[name="create"]').on('click',function () {
        var $elem = $('#create-menu-item-container');
        if ($(this).is(':checked')) {
            $elem.show();
        } else {
            $elem.hide();
        }
    }).triggerHandler('click');
});