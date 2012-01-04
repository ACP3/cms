$(document).ready(function() {
	$('ul.admin > li:has(ul) > a').click(function() {
		$(this).next('ul').stop(true, true).slideToggle('slow');
		return false;
	});
});