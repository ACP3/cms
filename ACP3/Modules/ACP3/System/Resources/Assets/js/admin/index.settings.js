(($) => {
    $(':radio[name="site_subtitle_mode"]').on('click change', function () {
        $('#site-subtitle-homepage-home-container').toggle(Number(this.value) !== 3);
    }).filter(':checked').triggerHandler('click');

    $(':radio[name="maintenance_mode"]').on('click change', function () {
        $('#maintenance-message-container').toggle(Number(this.value) === 1);
    }).filter(':checked').triggerHandler('click');

    $(':radio[name="mailer_smtp_auth"]').on('click change', function () {
        $('#mailer-smtp-2').toggle(Number(this.value) === 1);
    }).filter(':checked').triggerHandler('click');

    $('#mailer-type').on('change', function () {
        if ($(this).val() === 'smtp') {
            $('#mailer-smtp-1').show();
            $('input[name="mailer_smtp_auth"]:checked').trigger('click');
        } else {
            $('#mailer-smtp-1, #mailer-smtp-2').hide();
        }
    }).trigger('change');
})(jQuery);
