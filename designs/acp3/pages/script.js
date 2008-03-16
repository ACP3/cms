$(function() {
	function GetValue(id) {
		return $('#' + id).val();
	}
	function SwitchContainer() {
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
			$('#ShowHideSort').show();
		} else {
			$('#ShowHideSort').hide();
		}
	}
	// Seitentyp
	SwitchContainer();
	$('#mode').change(function() {
		SwitchContainer();
	});
	// Blöcke für die Navigationsleisten
	ShowHideSort();
	$('#blocks').change(function() {
		ShowHideSort();
	});
})