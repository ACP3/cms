$(document).ready(function() {
	function switchContainer() {
		var mode = $('#mode').val();
		var alias = $('#uri-alias');
		var page = $('#page-type');
		var module = $('#module-container');
		var hints = $('#link-hints');
		var link = $('#link-container');
		var static_page = $('#static-pages-container');

		if (mode == '1') {
			alias.hide();
			page.show();
			module.show();
			hints.hide();
			link.hide();
			static_page.hide();
		} else if (mode == '2') {
			alias.show();
			page.show();
			module.hide();
			hints.show();
			link.show();
			static_page.hide();
		} else if (mode == '3') {
			alias.hide();
			page.show();
			module.hide();
			hints.hide();
			link.show();
			static_page.hide();
		} else if (mode == '4') {
			alias.show();
			page.show();
			module.hide();
			hints.hide();
			link.hide();
			static_page.show();
		} else {
			alias.hide();
			page.hide();
			module.hide();
			hints.hide();
			link.hide();
			static_page.hide();
		}
	}
	// Seitentyp
	$('#mode').change(function() {
		switchContainer();
	}).change();

	// Nur die dem Block zugehörigen übergeordneten Seiten anzeigen
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