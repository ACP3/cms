$(document).ready(function() {
	$('input[name="form[overlay]"]').bind('click', function() {
		if ($(this).val() == 1) {
			$('#comments-container').hide();
		} else {
			$('#comments-container').show();
		}
	});

	$('input[name="form[overlay]"]:checked').trigger('click');
});