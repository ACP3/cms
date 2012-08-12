{$wysiwyg.editor}
{if $wysiwyg.advanced === true}
<script type="text/javascript">
$(document).ready(function() {
	var pagebreak = '<form id="page-break-form">';
	pagebreak+= '<fieldset>';
	pagebreak+= '<legend>{lang t="common|insert_page_break"}</legend>';
	pagebreak+= '<label for="toc-title" class="control-label">{lang t="common|title_for_toc"}</label>';
	pagebreak+= '<input type="text" id="toc-title">';
	pagebreak+= '</fieldset>';
	pagebreak+= '<div class="form-actions"><button class="btn">{lang t="common|submit"}</button></div>';
	pagebreak+= '</form>';
	pagebreak+= '<div id="page-break-link"><a href="#" class="btn">{lang t="common|insert_page_break"}</a></div>';

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