$(document).ready(function() {
	$(':checkbox[name="create"]').bind('onload click', function() {
		if ($(this).is(':checked')) {
			$('#create-item-container').show();
		} else {
			$('#create-item-container').hide();
		}
	}).trigger('onload');

	// Nur die zum Block gehörigen übergeordneten Seiten anzeigen
	$('#parent optgroup').hide();
	$('#block-id').change(function() {
		var block = $('#block-id option:selected').eq(0).text();
		$('#parent optgroup:not([label=\'' + block + '\'])').hide();
		$('#parent optgroup[label=\'' + block + '\']').show();
	}).change();
});