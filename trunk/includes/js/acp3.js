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
	$('#tabs fieldset:not(:first)').hide();
	
	$('#tabs #wrapper a').click(function() {
		$('#tabs #wrapper a').removeClass('selected');
		$(this).addClass('selected');
		$('#tabs fieldset:visible').hide();
		$('#tabs fieldset').filter(this.hash).show();
		return false;
	}).filter(':first').click();
})

