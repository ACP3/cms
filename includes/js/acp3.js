document.writeln('<style type="text/css">');
document.writeln('.hide { display:none; }');
document.writeln('</style>');

// Einträge markieren bzw. Markierung aufheben
function mark_entries(name, state)
{
	var fields = $('form input[@type=checkbox]');

	for (var i = 0; i < fields.length; i++) {
		if (fields[i].name.substr(0, name.length) == name) {
			fields[i].checked = state;
		}
	}
}
$(function() {
	// Akkordeon Menü
	$('#accordion fieldset dl:not(:first)').hide();

	$('#accordion fieldset > legend').each(function() {
		var legend = $(this).text();
		$(this).empty().append($('<a href="#">'+ legend +'</a>'));
	});
	$('#accordion fieldset > legend a').click(function() {
		var fieldset = $(this).parent().parent();

		if (!fieldset.children('dl').is(':visible') && !$('#accordion fieldset dl').is(':animated')) {
			$('dl:visible').slideUp();
			fieldset.children('dl').slideDown();
		}
		return false;
	});
})

