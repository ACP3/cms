{js_libraries enable="bootbox"}
<script type="text/javascript">
function mark_entries(name, action)
{
	var fields = $('form .table tbody input[name="' + name + '[]"]:visible');

	jQuery.each(fields, function() {
		if (action == 'add') {
			$(this).prop('checked', true).parents('tr:first').addClass('selected');
		} else {
			$(this).prop('checked', false).parents('tr:first').removeClass('selected');
		}
	});
}

$(document).ready(function() {
	$('#mark-all').click(function() {
		if ($(this).is(':checked')) {
			mark_entries('{$checkbox_name}', 'add');
		} else {
			mark_entries('{$checkbox_name}', 'remove');
		}
	});

	// Checkbox durch Klick auf Tabellenzeile markieren
	$('form > .table > tbody > tr').filter(':has(:checkbox:checked)').addClass('selected').end().click(function(event) {
		if (event.target.type !== 'checkbox') {
			$(':checkbox', this).trigger('click');
		}
	}).find(':checkbox').click(function() {
		$(this).parents('tr:first').toggleClass('selected');
		if ($('.table tbody tr:has(:checkbox):visible').length == $('.table tbody tr.selected:visible').length) {
			$('#mark-all').prop('checked', true);
		} else {
			$('#mark-all').prop('checked', false);
		}
	});

	$('form #adm-list input[type=image]').click(function() {
		var entries = $('form .table :checkbox:checked') || [];
		var ary = '';

		jQuery.each(entries, function() {
			ary = ary + $(this).val() + '|';
		});

		if (ary != '') {
			bootbox.confirm('{lang t="common|confirm_delete"}', '{lang t="common|no"}', '{lang t="common|yes"}', function(result) {
				if (result) {
					location.href = $('.table').parent('form').attr('action') + 'entries_' + ary.substr(0, ary.length - 1) + '/action_confirmed/';
				}
			});
		} else {
			bootbox.alert('{lang t="common|no_entries_selected"}');
		}
		return false;
	});
});
</script>