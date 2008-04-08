document.writeln('<style type="text/css">');
document.writeln('.hide { display:none; }');
document.writeln('</style>');

// Einträge markieren bzw. Markierung aufheben
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
$(function() {
	$("#tabs fieldset").each(function() {
		if ($(this).attr('id')) {
			var header = $(this).prev('h3').text();
			var id = $(this).attr('id');
			$(this).prev('h3').empty().append($('<a href="#' + id + '">' + header + '</a>'));
		}
	});
	$('#tabs').prepend('<div id="wrapper"></div>');
	$('#tabs h3').appendTo('#wrapper');
	$('#tabs #wrapper a:first').addClass('selected');
	$('#tabs fieldset:not(:first)').hide();
	
	$('#tabs #wrapper a').click(function() {
		var tab = $(this).attr('href').substr(1);
		if (!$('#tabs fieldset#' + tab).is(':visible') && !$('#tabs fieldset').is(':animated')) {
			$('#tabs #wrapper a').removeClass('selected');
			$(this).addClass('selected');
			$('#tabs fieldset:visible').animate({ height: 'hide', opacity: 'hide' }, 'fast');
			$('#tabs fieldset#' + tab).animate({ height: 'show', opacity: 'show' }, 'slow');
		}
		return false;
	});
})

