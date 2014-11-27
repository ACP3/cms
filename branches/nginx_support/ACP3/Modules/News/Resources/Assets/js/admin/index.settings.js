jQuery(document).ready(function ($) {
    $('input[name="readmore"]').on('click',function () {
        var $elem = $('#readmore-container');
        if ($(this).val() == 1) {
            $elem.show();
        } else {
            $elem.hide();
        }
    }).filter(':checked').click();
});
