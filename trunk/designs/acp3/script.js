$(document).ready(function() {
	// Verschachtelte Navigation
	$('#head ul li:has(ul)').hover(function() {
		$(this).children('ul').show();
	}, function() {
		$(this).children('ul').hide();
	});

	// jQuery UI Tabs
	$('#tabs').tabs({ cookie: { expires: 1 }});
})