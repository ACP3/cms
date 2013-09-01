{$wysiwyg.editor}
{if $wysiwyg.advanced === true}
{js_libraries enable="bootstrap"}
<div id="page-break-form" class="modal hide">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>{lang t="system|insert_page_break"}</h3>
	</div>
	<div class="modal-body form-horizontal">
		<div class="form-group">
			<label for="toc-title" class="col-lg-2 control-label">{lang t="system|title_for_toc"}</label>
			<div class="col-lg-10"><input class="form-control" type="text" id="toc-title"></div>
		</div>
		<div class="form-actions"><button class="btn btn-default">{lang t="system|submit"}</button></div>
	</div>
</div>
<div id="page-break-link" class="text-left"><a href="#" class="btn btn-default">{lang t="system|insert_page_break"}</a></div>
<script type="text/javascript">
$(document).ready(function() {
	$('#page-break-link a').click(function(e) {
		$('#page-break-form').modal('show');
		e.preventDefault();
	});

	$('#page-break-form .form-actions button').click(function(e) {
		if ($('#toc-title').val().length > 0) {
			var text = '<hr class="page-break" title="' + $('#toc-title').val() + '" />';
		} else {
			var text = '<hr class="page-break" />';
		}

		{$wysiwyg.advanced_replace_content}
		$('#page-break-form').modal('hide');

		e.preventDefault();
	});
});
</script>
{/if}