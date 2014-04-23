$(function () {
    $(':radio[name="mailer_smtp_auth"]').bind('onload change', function () {
        var $elem = $('#mailer-smtp-2');
        if ($(this).val() == 1) {
            $elem.show();
        } else {
            $elem.hide();
        }
    });

    $('#mailer-type').bind('change',function () {
        if ($(this).val() === 'smtp') {
            $('#mailer-smtp-1').show();
            $('input[name="mailer_smtp_auth"]:checked').trigger('click');
        } else {
            $('#mailer-smtp-1, #mailer-smtp-2').hide();
        }
    }).trigger('change');
});