jQuery(function($) {
	$('ul.admin > li:has(ul) > a').click(function() {
		if (!$(this).next('ul').is(':animated')) {
			$(this).next('ul').slideToggle('slow');
		}
		return false;
	});
});