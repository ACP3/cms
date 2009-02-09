/**
 * Einträge markieren bzw. Markierung aufheben
 **/
function mark_entries(name, action)
{
	var fields = $('form :checkbox');

	jQuery.each(fields, function() {
		if ($(this).attr('name') == name + '[]') {
			if (action == 'add') {
				$(this).attr('checked', 'checked');
			} else {
				$(this).removeAttr('checked');
			}
		}
	});
}

jQuery(function($) {
	// Verschachtelte Navigation
	$('#head ul li:has(ul)').hover(function() {
		$(this).children('ul').show();
	}, function() {
		$(this).children('ul').hide();
	});

	// jQuery UI Tabs
	$('#tabs').tabs({ cookie: { expires: 30 }});

	// Checkbox durch Klick auf Tabellenzeile markieren
	$('table.acp-table tr').filter(':has(:checkbox:checked)').addClass('selected').end().click(function(event) {
		if (event.target.type !== 'checkbox') {
			$(':checkbox', this).trigger('click');
		}
	}).find(':checkbox').click(function() {
		$(this).parents('tr:first').toggleClass('selected');
	});
})