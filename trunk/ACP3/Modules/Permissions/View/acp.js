$(document).ready(function() {
	$('#resources-table tbody tr:not(.sub-table-header)').hide();
	$('#resources-table .sub-table-header').click(function() {
		$(this).next('tr').toggle();
		var visibleLength = $('#resources-table tbody tr:has(:checkbox):visible').length;
		var allVisibleChecked = visibleLength > 0 && visibleLength === $('#resources-table tbody tr.selected:visible').length;
		$('#mark-all').prop('checked', allVisibleChecked);
	});
});