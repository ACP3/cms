$(document).ready(function() {
	// Verschachtelte Navigation
	$('#header ul li:has(ul)').hover(function() {
		$(this).children('ul').show();
	}, function() {
		$(this).children('ul').hide();
	});

	$('.dropdown-toggle').dropdown();
})