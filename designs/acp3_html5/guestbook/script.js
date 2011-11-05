$(document).ready(function() {
	$('#create-link').colorbox({
		width: '40%',
		height: '68%',
		iframe: true,
		onClosed: function() {
			window.location.reload();
		}
	});
	$('#create-link').attr('href', $('#create-link').attr('href').replace('create', 'create/design_simple'));
});