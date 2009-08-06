jQuery(function($) {
	$('#external-filesize').hide();

	if ($('#external').is(':checked')) {
		$('#file-external').show();
		$('#external-filesize').show();
		$('#file-internal').hide();
	} else {
		$('#file-external').hide();
		$('#external-filesize').hide();
		$('#file-internal').show();
	}
	$('#external').click(function() {
		$('#file-external').toggle();
		$('#external-filesize').toggle();
		$('#file-internal').toggle();
	});
});