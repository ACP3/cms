{$wysiwyg.editor}
{if $wysiwyg.advanced === true}
<script type="text/javascript">
$(document).ready(function() {
	var pagebreak = '<form id="page-break-form">';
	pagebreak+= '<fieldset>';
	pagebreak+= '<legend>{lang t="common|insert_page_break"}</legend>';
	pagebreak+= '<dl>';
	pagebreak+= '<dt><label for="toc-title">{lang t="common|title_for_toc"}</label></dt>';
	pagebreak+= '<dd><input type="text" id="toc-title"></dd>';
	pagebreak+= '</dl>';
	pagebreak+= '</fieldset>';
	pagebreak+= '<div class="form-bottom"><button class="form">{lang t="common|submit"}</button></div>';
	pagebreak+= '</form>';
	pagebreak+= '<div id="page-break-link"><a href="#" class="form">{lang t="common|insert_page_break"}</a></div>';

	$('#{$wysiwyg.id}').after(pagebreak);
	$('#page-break-form').hide();
	$('#page-break-link a').click(function(e) {
		$.fancybox.open($('#page-break-form'), { type: 'inline', autoSize: false, width: '20%', height: 'auto' });
		e.preventDefault();
	});

	$('#page-break-form button').click(function(e) {
		if ($('#toc-title').val().length > 0) {
			var text = '<hr class="page-break" title="' + $('#toc-title').val() + '" />';
		} else {
			var text = '<hr class="page-break" />';
		}

		{$wysiwyg.advanced_replace_content}
		$.fancybox.close();

		e.preventDefault();
	});
});
</script>
{/if}