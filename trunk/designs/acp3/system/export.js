function mark_options(action)
{
	if (action == 'add') {
		$('form #tables option').attr('selected', 'selected');
	} else {
		$('form #tables option').removeAttr('selected');
	}
}

$(function() {
	$('input[name="export_type"]').click(function() {
		if (($(this).attr('id') == 'complete' || $(this).attr('id') == 'structure')) {
			$('#options-container').show();
		} else {
			$('#options-container').hide();
		}
	}).click();
});