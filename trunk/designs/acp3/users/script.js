$(document).ready(function() {
	$('#nav-mods-link').toggle(function() {
		$('#nav-mods').slideDown('slow');
		return false;
	},function() {
		$('#nav-mods').slideUp('slow');
	});
	$('#nav-system-link').toggle(function() {
		$('#nav-system').slideDown('slow');
		return false;
	},function() {
		$('#nav-system').slideUp('slow');
	});
});