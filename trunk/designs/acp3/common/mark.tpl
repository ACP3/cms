<script type="text/javascript">
function mark_entries(name, action)
{
	var fields = $('form .acp-table tbody input[name="' + name + '[]"]:visible');

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
	$('form > table.acp-table > tbody > tr').filter(':has(:checkbox:checked)').addClass('selected').end().click(function(event) {
		if (event.target.type !== 'checkbox') {
			$(':checkbox', this).trigger('click');
		}
	}).find(':checkbox').click(function() {
		$(this).parents('tr:first').toggleClass('selected');
		if ($('.acp-table tbody tr:has(:checkbox):visible').length == $('.acp-table tbody tr.selected:visible').length) {
			$('#mark-all').prop('checked', true);
		} else {
			$('#mark-all').prop('checked', false);
		}
	});

	$('.acp-table').after('<' + 'div id="dialog"><' + 'h5 style="text-align:center"><' + '/h5><' + '/div>');
	$('#dialog').dialog({
		autoOpen: false,
		draggable: false,
		resizable: false,
		modal: true,
		overlay: {
			backgroundColor: '#000',
			opacity: 0.5
		}
	});

	$('form #adm-list input[type=image]').click(function() {
		var entries = $('form .acp-table :checkbox:checked') || [];
		var ary = '';

		jQuery.each(entries, function() {
			ary = ary + $(this).val() + '|';
		});

		if (ary != '') {
			$('#dialog h5').text('{lang t="common|confirm_delete"}');
			$('#dialog').dialog('option', {
				buttons: {
					{lang t="common|no"}: function() {
						$(this).dialog('close');
					},
					{lang t="common|yes"}: function() {
						location.href = $('.acp-table').parent('form').attr('action') + 'entries_' + ary.substr(0, ary.length - 1) + '/action_confirmed/';
					}
				}
			}).dialog('open');
		} else {
			$('#dialog h5').text('{lang t="common|no_entries_selected"}');
			$('#dialog').dialog('option', { buttons: {} }).dialog('open');
		}
		return false;
	});
});
</script>