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
	var match = '';
	$('#tabs').prepend('<div id="wrapper"></div>');
	$("#tabs fieldset").each(function(i) {
		if ($(this).attr('id')) {
			var header = $(this).children('legend').text();
			var id = $(this).attr('id');
			$('#tabs #wrapper').append($('<a href="#' + id + '">' + header + '</a>'));
			if (window.location.hash.substr(1) == id) {
				match = id;
				$('#tabs #wrapper a:eq(' + i + ')').addClass('selected');
			}
		}
	});
	$('#tabs legend').remove();
	if (match != '') {
		$('#tabs fieldset:not(#' + match + ')').hide();
	} else {
		$('#tabs #wrapper a:first').addClass('selected');
		$('#tabs fieldset:not(:first)').hide();
	}
	
	$('#tabs #wrapper a').click(function() {
		$('#tabs #wrapper a').removeClass('selected');
		$(this).addClass('selected');
		$('#tabs fieldset:visible').hide();
		$('#tabs fieldset').filter(this.hash).show();
		return false;
	});
})

