$(document).ready(function() {
	$('input[name="readmore"]').bind('click', function() {
		if ($(this).val() == 1) {
			$('#readmore-container').show();
		} else {
			$('#readmore-container').hide();
		}
	});

	$('input[name="readmore"]:checked').trigger('click');
});