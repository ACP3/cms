jQuery(function($) {
	// Sprachdropdown
	$('#languages input[type=submit]').hide();
	$('#lang').change(function() {
		$('#languages').submit();
	});

	// Tabelle mit den SQL Abfragen aufklappen/zuklappen
	$('#queries-link').click(function() {
		$('#queries').slideToggle('fast');
		return false;
	});
})