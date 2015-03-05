jQuery(document).ready(function ($) {
    $('input[name="overlay"]').bind('click',function () {
        var $elem = $('#comments-container');
        if ($(this).val() == 1) {
            $elem.hide();
        } else {
            $elem.show();
        }
    }).filter(':checked').trigger('click');
});