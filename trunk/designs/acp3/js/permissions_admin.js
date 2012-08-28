$(document).ready(function() {
	$('.hide:not(:first)').hide();
	$('.sub-table-header').click(function() {
		$('.' + $(this).prop('id')).toggle();
		var visible_length = $('#resources-table tbody tr:has(:checkbox):visible').length;
		if (visible_length > 0 && visible_length == $('#resources-table tbody tr.selected:visible').length) {
			$('#mark-all').prop('checked', true);
		} else {
			$('#mark-all').prop('checked', false);
		}
	});
});