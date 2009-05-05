function mark_options(action)
{
	if (action == 'add') {
		$('form #tables option').attr('selected', 'selected');
	} else {
		$('form #tables option').removeAttr('selected');
	}
}
