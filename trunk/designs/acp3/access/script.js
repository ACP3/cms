function mark_permissions(permission) {
	var checkbox = '';
	switch(permission) {
		case 'read':
			checkbox = '.access-read';
			break;
		case 'create':
			checkbox = '.access-create';
			break;
		case 'edit':
			checkbox = '.access-edit';
			break;
		case 'delete':
			checkbox = '.access-delete';
			break;
		case 'full':
			checkbox = '.access-full';
			break;
	}

	var boxes = jQuery('table.acp-table input' + checkbox);
	if (boxes.is(':checked')) {
		if (permission != 'full' && jQuery('table.acp-table input.access-full').is(':checked')) {
			jQuery('table.acp-table input.access-full').removeAttr('checked');
		}
		boxes.removeAttr('checked');
	} else if (permission == 'full') {
		jQuery('table.acp-table input.access-read, table.acp-table input.access-create, table.acp-table input.access-edit, table.acp-table input.access-delete, table.acp-table input.access-full').attr('checked', 'checked');
	} else {
		boxes.attr('checked', 'checked');
	}
}

$(document).ready(function() {
	$('table.acp-table input').click(function() {
		if ($(this).attr('class') != 'access-full checkbox' && !$(this).is(':checked') &&
			$(this).parent('td').parent('tr').children('td').children('input.access-full:checked')) {
			$(this).parent('td').parent('tr').children('td').children('input.access-full:checked').removeAttr('checked');
			$(this).removeAttr('checked');
		} else if ($(this).attr('class') == 'access-full checkbox' && $(this).is(':checked')) {
			$(this).parent('td').parent('tr').children('td').children('input').attr('checked', 'checked');
		}
	});

	$('table.acp-table a.mark-horizontal').click(function() {
		if ($(this).parent().nextAll().children('input').is(':checked')) {
			$(this).parent().nextAll().children('input').removeAttr('checked');
		} else {
			$(this).parent().nextAll().children('input').attr('checked', 'checked');
		}
	});
});