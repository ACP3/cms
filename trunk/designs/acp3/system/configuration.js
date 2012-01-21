$(function() {
	$('input[name="form[mailer_smtp_auth]"]').bind('click', function() {
		if ($(this).val() == 1) {
			$('#mailer-smtp-2').show();
		} else {
			$('#mailer-smtp-2').hide();
		}
	});
	$('#mailer-type').bind('change', function() {
		if ($(this).val() == 'smtp') {
			$('#mailer-smtp-1').show();
			$('input[name="form[mailer_smtp_auth]"]:checked').trigger('click');
		} else {
			$('#mailer-smtp-1, #mailer-smtp-2').hide();
		}
	});

	$('#mailer-type').trigger('change');
});