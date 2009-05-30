jQuery(function($) {
	function switchContainer() {
		var mode = $('#mode').val();
		var page = $('#page-type');
		var module = $('#module-container');
		var hints = $('#link-hints');
		var link = $('#link-container');

		if (mode == '1') {
			page.show();
			module.show();
			hints.hide();
			link.hide();
		} else if (mode == '2') {
			page.show();
			module.hide();
			hints.show();
			link.show();
		} else if (mode == '3') {
			page.show();
			module.hide();
			hints.hide();
			link.show();
		} else {
			page.hide();
			module.hide();
			hints.hide();
			link.hide();
		}
	}
	// Seitentyp
	$('#mode').change(function() {
		switchContainer();
	}).change();

	// Nur die zum Block gehörigen übergeordneten Seiten anzeigen
	$('#parent optgroup').hide();

	var def_block = 0;
	$('#block_id option').each(function() {
		if ($(this).is(':selected')) {
			def_block = $('#block_id option').index(this);
		}
	});

	$('#block_id').change(function() {
		var block = $('#block_id option:selected').eq(0).text();
		$('#parent optgroup:not([label=\'' + block + '\'])').hide();
		$('#parent optgroup[label=\'' + block + '\']').show();

		$('#block_id option').each (function() {
			if ($(this).is(':selected') && $('#block_id option').index(this) != def_block) {
				$('#parent optgroup option:selected').removeAttr('selected');
			}
		});
	}).change();
})