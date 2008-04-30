$(function() {
	$('#head ul li:has(ul)').hover(function() {
		$(this).children('ul').show();
	}, function() {
		$(this).children('ul').hide();
	});
});