$(document).ready(function() {
	$('#create-link').fancybox({
		type: 'iframe',
		width: '40%',
		height: '68%',
		padding: 0,
		afterClose: function() {
			window.location.reload();
		}
	});
	$('#create-link').attr('href', $('#create-link').attr('href').replace('create', 'create/design_simple'));
});