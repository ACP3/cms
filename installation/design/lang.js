$(document).ready(function() {
	$('#languages input[type=submit]').hide();
	$('#lang').change(function() {
		$('#languages').submit();
	});
})