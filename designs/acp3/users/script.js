$(function() {
	$('#nav-mods-link').click(function() {
		if (!$('#nav-mods').is(':animated')) {
			$('#nav-mods').slideToggle('slow');
		}
		return false;
	});
	$('#nav-system-link').click(function() {
		if (!$('#nav-system').is(':animated')) {
			$('#nav-system').slideToggle('slow');
		}
		return false;
	});
});