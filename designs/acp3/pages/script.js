$(document).ready(function() {
	function GetValue(id) {
		return $('#' + id).val();
	}
	function SwitchBackground() {
		var mode = GetValue('mode');

		if (mode == '1') {
			$('#static_page').show();
			$('#hyperlink').hide();
		} else if (mode == '2' || mode == '3') {
			$('#static_page').hide();
			$('#hyperlink').show();
		} else {
			$('#static_page').hide();
			$('#hyperlink').hide();
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