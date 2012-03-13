jQuery(function($) {
	// Sprachdropdown
	$('#languages :submit').hide();
	$('#lang').change(function() {
		$('#languages').submit();
	});

	// Tabelle mit den SQL Abfragen aufklappen/zuklappen
	$('#queries').hide();
	$('#queries-link').click(function() {
		$('#queries').slideToggle('fast');
		return false;
	});

	// jQuery UI Tabs
	$('#tabs').tabs();
})