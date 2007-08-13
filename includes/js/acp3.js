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
$(document).ready(function() {
	// Akkordeon Menü
	$('#accordion fieldset dl:not(:first)').hide();
	$('#accordion fieldset legend').click(function(){
		$('dl:visible').animate({height: 'hide', opacity: 'hide'}, 'fast');
		$(this).parent().children('dl').animate({height: 'show', opacity: 'show'}, 'slow');
		return false;
	});
})

