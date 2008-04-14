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
$(function() {
	/**
	 * Tabs
	 **/
	var match = 0;
	$('#tabs').prepend('<div id="wrapper"></div>');
	$('#tabs > fieldset').each(function(i) {
		if ($(this).attr('id')) {
			var header = $(this).children('legend').text();
			var id = $(this).attr('id');
			$('#tabs #wrapper').append($('<a href="#' + id + '">' + header + '</a>'));
			if (window.location.hash.substr(1) == id) {
				match = i;
			}
		}
	});
	$('#tabs > fieldset > legend').remove();
	$('#tabs > fieldset:not(:eq(' + match + '))').hide();
	
	$('#tabs #wrapper a').click(function() {
		$('#tabs #wrapper a').removeClass('selected');
		$(this).addClass('selected');
		$('#tabs > fieldset:visible').hide();
		$('#tabs fieldset').filter(this.hash).show();
		return false;
	}).filter(':eq(' + match + ')').click();
})