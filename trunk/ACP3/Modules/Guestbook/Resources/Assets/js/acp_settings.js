jQuery(document).ready(function ($) {
    $('#notify').bind('change',function () {
        var $elem = $('#notify-email').parents('.form-group');
        if ($(this).val() == 0) {
            $elem.hide();
        } else {
            $elem.show();
        }
    }).children('option:selected').trigger('change');
});
