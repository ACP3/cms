document.writeln('<style type="text/css">');
document.writeln('.hide { display:none; }');
document.writeln('</style>');

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
	$('#tabs').tabs({ cookie: { expires: 30 }});
})