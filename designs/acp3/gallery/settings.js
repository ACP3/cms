$(document).ready(function() {
	$('input[name="overlay"]').bind('click', function() {
		if ($(this).val() == 1) {
			$('#comments-container').hide();
		} else {
			$('#comments-container').show();
		}
	});

	$('input[name="overlay"]:checked').trigger('click');
});