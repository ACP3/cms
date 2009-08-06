function mark_permissions(action) {
	var checkbox = '';
	switch(action) {
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
	}

	var boxes = jQuery('table.acp-table input' + checkbox);
	if (boxes.is(':checked')) {
		boxes.removeAttr('checked');
	} else {
		boxes.attr('checked', 'checked');
	}
}