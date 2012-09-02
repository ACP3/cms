$(document).ready(function() {
	$('input[name="action"]').bind('click', function() {
		if ($(this).val() == 1) {
			$('#test-newsletter').show();
		} else {
			$('#test-newsletter').hide();
		}
	});
	$('input[name="action"]:checked').trigger('click');
});