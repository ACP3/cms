$(document).ready(function() {
	$('input[name="form[action]"]').bind('click', function() {
		if ($(this).val() == 1) {
			$('#test-newsletter').show();
		} else {
			$('#test-newsletter').hide();
		}
	});
	$('input[name="form[action]"]:checked').trigger('click');
});