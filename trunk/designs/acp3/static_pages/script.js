$(document).ready(function() {
	$(':radio[name="form[create]"]').bind('click', function() {
		if ($(this).val() == 1) {
			$('#create-item-container').show();
		} else {
			$('#create-item-container').hide();
		}
	});
	$(':radio[name="form[create]"]:checked').trigger('click');

	// Nur die zum Block gehörigen übergeordneten Seiten anzeigen
	$('#parent optgroup').hide();
	$('#block-id').change(function() {
		var block = $('#block-id option:selected').eq(0).text();
		$('#parent optgroup:not([label=\'' + block + '\'])').hide();
		$('#parent optgroup[label=\'' + block + '\']').show();
	}).change();
});