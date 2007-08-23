$(document).ready(function() {
	$('#nav_mods_link').toggle(function() {
		$('#nav_mods').slideDown('slow');
		return false;
	},function() {
		$('ul#nav_mods').slideUp('slow');
	});
	$('#nav_system_link').toggle(function() {
		$('#nav_system').slideDown('slow');
		return false;
	},function() {
		$('#nav_system').slideUp('slow');
	});
});