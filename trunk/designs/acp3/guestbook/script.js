$(document).ready(function() {
	$('#create-link').click(function(e) {
		if (e.which == 1) {
			$.fancybox.open({ href: $(this).attr('href') + 'layout_simple/', title: $(this).attr('title') }, {
				type: 'iframe',
				autoSize: true,
				padding: 0,
				afterClose: function() {
					location.reload();
					return;
				}
			});
			e.preventDefault();
		}
	});
});