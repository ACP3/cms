jQuery(function($) {
	function switchContainer() {
		var mode = $('#mode').val();
		var module = $('#module-container');
		var hints = $('#link-hints');
		var link = $('#link-container');

		if (mode == '1') {
			module.show();
			hints.hide();
			link.hide();
		} else if (mode == '2') {
			module.hide();
			hints.show();
			link.show();
		} else if (mode == '3') {
			module.hide();
			hints.hide();
			link.show();
		} else {
			module.hide();
			hints.hide();
			link.hide();
		}
	}
	// Seitentyp
	switchContainer();
	$('#mode').change(function() {
		switchContainer();
	});
})