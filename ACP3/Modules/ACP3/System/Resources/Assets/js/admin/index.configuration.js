jQuery(document).ready(function ($) {
    $(':radio[name="cookie_consent_is_enabled"]').on('click change', function () {
        $('#cookie-consent-container').toggle(this.value == 1);
    }).filter(':checked').triggerHandler('click');

    $(':radio[name="maintenance_mode"]').on('click change', function () {
        $('#maintenance-message-container').toggle(this.value == 1);
    }).filter(':checked').triggerHandler('click');

    $(':radio[name="mailer_smtp_auth"]').on('click change', function () {
        $('#mailer-smtp-2').toggle(this.value == 1);
    }).filter(':checked').triggerHandler('click');

    $('#mailer-type').on('change', function () {
        if ($(this).val() === 'smtp') {
            $('#mailer-smtp-1').show();
            $('input[name="mailer_smtp_auth"]:checked').trigger('click');
        } else {
            $('#mailer-smtp-1, #mailer-smtp-2').hide();
        }
    }).trigger('change');
});
