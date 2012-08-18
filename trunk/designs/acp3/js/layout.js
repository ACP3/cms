$(document).ready(function() {
	$('.navigation-main li:has(ul)').click(function(e) {
		if (!$(this).children('ul').is(':visible')) {
			$(this).children('ul').show();
			e.preventDefault();
		}
	}).mouseleave(function() {
		$(this).children('ul').hover(function() {
			$(this).data('hover', true);
		}, function() {
			$(this).data('hover', false);
		}).data('hover', false);

		if (!$(this).children('ul').data('hover'))
			$(this).children('ul').hide();
	});
})