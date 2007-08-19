$(document).ready(function() {
	if ($('#external')[0].checked) {
		$('#file_external').show();
		$('#file_internal').hide();
	} else {
		$('#file_external').hide();
		$('#file_internal').show();
	}
	$('#external').click(function() {
		$('#file_external').toggle();
		$('#file_internal').toggle();
	});
});