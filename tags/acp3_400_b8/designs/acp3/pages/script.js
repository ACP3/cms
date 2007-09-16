$(document).ready(function() {
	function GetValue(id) {
		return $('#' + id).val();
	}
	function SwitchBackground() {
		var mode = GetValue('mode');
		var light = '#fff';
		var dark = '#eee';

		if (mode == '1') {
			$('#static_page').css({ background: dark });
			$('#hyperlink').css({ background: light });
		} else if (mode == '2' || mode == '3') {
			$('#static_page').css({ background: light });
			$('#hyperlink').css({ background: dark });
		} else {
			$('#static_page').css({ background: light });
			$('#hyperlink').css({ background: light });
		}
	}
	function ShowHideSort() {
		var blocks = GetValue('blocks');
		if (blocks != '0' && blocks != '') {
			$('#sort_label').css({ display: 'block' });
			$('#sort_input').css({ display: 'block' });
		} else {
			$('#sort_label').css({ display: 'none' });
			$('#sort_input').css({ display: 'none' });
		}
	}
	// Seitentyp
	SwitchBackground();
	$('#mode').change(function() {
		SwitchBackground();
	});
	// Blöcke für die Navigationsleisten
	ShowHideSort();
	$('#blocks').change(function() {
		ShowHideSort();
	});
})