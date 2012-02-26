$(document).ready(function() {
	$('#create-link').attr('href', $('#create-link').attr('href') + 'layout_simple/');
	$('#create-link').fancybox({
		type: 'iframe',
		autoSize: true,
		padding: 0,
		afterClose: function() {
			window.location.reload();
		}
	});
});