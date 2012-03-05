$(document).ready(function() {
	var alias = $('#alias').parents('dl');
	var module = $('#module-container');
	var hints = $('#link-hints');
	var link = $('#link-container');
	var static_page = $('#static-pages-container');
	var target = $('#target-container');

	// Wenn Menüpunkt nicht angezeigt werden soll, Linkziel verstecken
	$('input[name="display"]').change(function() {
		if ($(this).val() == 0) {
			target.hide();
		} else {
			target.show();
		}
	});

	var currentMode = $('#mode').val();
	// Seitentyp
	$('#mode').change(function() {
		var mode = $(this).val();

		// SEO Tab bei einem externen Hyperlink deaktivieren
		if (mode == 3) {
			$('#tabs').tabs({disabled: [ 2 ]});
		} else {
			$('#tabs').tabs({disabled: []});
		}

		if (mode == 1) {
			alias.hide();
			module.show();
			hints.hide();
			link.hide();
			static_page.hide();

			// Modul im Dropdown-Men selektieren, falls zuvor als Modus eine dynamische Seite aktiv war
			if (currentMode == 2) {
				var match = $('#uri').val().match(/^([a-z\d_\-]+)\/([a-z\d_\-]+\/)+$/);
				if (!$('#uri').val().match(/^static_pages\/list\/id_(\d+)\/$/) && match[1] != null && $('#module option[value="' + match[1] + '"]').length > 0) {
					$('#module').val(match[1]);
				}
			}
		} else if (mode == 2) {
			alias.show();
			module.hide();
			hints.show();
			link.show();
			static_page.hide();
		} else if (mode == 3) {
			module.hide();
			hints.hide();
			link.show();
			static_page.hide();
		} else if (mode == 4) {
			alias.show();
			module.hide();
			hints.hide();
			link.hide();
			static_page.show();
		} else {
			alias.hide();
			module.hide();
			hints.hide();
			link.hide();
			static_page.hide();
		}

		currentMode = mode;
	}).change();

	$('#uri').blur(function() {
		var match = $(this).val().match(/^static_pages\/list\/id_(\d+)\/$/);
		if (match[1] != null && $('#static-pages option[value="' + match[1] + '"]').length > 0) {
			$('#mode').val(4).change();
			$('#static-pages').val(match[1]);
		}
	});

	// Nur die dem Block zugehörigen übergeordneten Seiten anzeigen
	$('#parent optgroup').hide();

	var def_block = $('#block-id option:selected').index() || 0;

	$('#block-id').change(function() {
		var block = $('#block-id option:selected').eq(0).text();
		$('#parent optgroup:not([label=\'' + block + '\'])').hide();
		$('#parent optgroup[label=\'' + block + '\']').show();

		$('#block-id option').each(function() {
			if ($(this).is(':selected') && $('#block-id option').index(this) != def_block) {
				$('#parent optgroup option:selected').removeAttr('selected');
			}
		});
	}).change();
});