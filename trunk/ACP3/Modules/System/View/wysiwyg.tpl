{$wysiwyg.editor}
{if $wysiwyg.advanced === true}
{js_libraries enable="bootstrap"}
<div id="page-break-form" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h4 class="modal-title">{lang t="system|insert_page_break"}</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label for="toc-title">{lang t="system|title_for_toc"}</label>
					<input type="text" id="toc-title" class="form-control">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button class="btn btn-primary">{lang t="system|submit"}</button>
			</div>
		</div>
	</div>
</div>
<div id="page-break-link" class="align-left">
	<a href="#" class="btn btn-default" data-toggle="modal" data-target="#page-break-form">{lang t="system|insert_page_break"}</a>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$('#page-break-form .modal-footer button.btn-primary').click(function(e) {
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