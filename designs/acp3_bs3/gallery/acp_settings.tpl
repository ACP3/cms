{if isset($error_msg)}
	{$error_msg}
{/if}
<script type="text/javascript">
	$(document).ready(function() {
		$('input[name="overlay"]').bind('click', function() {
			var $elem = $('#comments-container');
			if ($(this).val() == 1) {
				$elem.hide();
			} else {
				$elem.show();
			}
		}).filter(':checked').trigger('click');
	});
</script>
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|general_statements"}</a></li>
			<li><a href="#tab-2" data-toggle="tab">{lang t="gallery|image_dimensions"}</a></li>
		</ul>
		<div class="tab-content">
			<div id="tab-1" class="tab-pane active">
				<div class="form-group">
					<label for="date-format" class="col-lg-2 control-label">{lang t="system|date_format"}</label>
					<div class="col-lg-10">
						<select class="form-control" name="dateformat" id="date-format">
							{foreach $dateformat as $row}
								<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="sidebar-entries" class="col-lg-2 control-label">{lang t="system|sidebar_entries_to_display"}</label>
					<div class="col-lg-10">
						<select class="form-control" name="sidebar" id="sidebar-entries">
							{foreach $sidebar_entries as $row}
								<option value="{$row.value}"{$row.selected}>{$row.value}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="{$overlay.0.id}" class="col-lg-2 control-label">{lang t="gallery|use_overlay"}</label>
					<div class="col-lg-10">
						<div class="btn-group" data-toggle="buttons">
							{foreach $overlay as $row}
								<label for="{$row.id}" class="btn btn-default">
									<input type="radio" name="overlay" id="{$row.id}" value="{$row.value}"{$row.checked}>
									{$row.lang}
								</label>
							{/foreach}
						</div>
						<p class="help-block">{lang t="gallery|use_overlay_description"}</p>
					</div>
				</div>
				{if isset($comments)}
					<div id="comments-container" class="form-group">
						<label for="{$comments.0.id}" class="col-lg-2 control-label">{lang t="system|allow_comments"}</label>
						<div class="col-lg-10">
							<div class="btn-group" data-toggle="buttons">
								{foreach $comments as $row}
									<label for="{$row.id}" class="btn btn-default">
										<input type="radio" name="comments" id="{$row.id}" value="{$row.value}"{$row.checked}>
										{$row.lang}
									</label>
								{/foreach}
							</div>
						</div>
					</div>
				{/if}
			</div>
			<div id="tab-2" class="tab-pane">
				<div class="form-group">
					<label for="thumbwidth" class="col-lg-2 control-label">{lang t="gallery|thumb_image_width"}</label>
					<div class="col-lg-10">
						<input class="form-control" type="number" name="thumbwidth" id="thumbwidth" value="{$form.thumbwidth}">
						<p class="help-block">{lang t="system|statements_in_pixel"}</p>
					</div>
				</div>
				<div class="form-group">
					<label for="thumbheight" class="col-lg-2 control-label">{lang t="gallery|thumb_image_height"}</label>
					<div class="col-lg-10">
						<input class="form-control" type="number" name="thumbheight" id="thumbheight" value="{$form.thumbheight}">
						<p class="help-block">{lang t="system|statements_in_pixel"}</p>
					</div>
				</div>
				<div class="form-group">
					<label for="width" class="col-lg-2 control-label">{lang t="gallery|image_width"}</label>
					<div class="col-lg-10">
						<input class="form-control" type="number" name="width" id="width" value="{$form.width}">
						<p class="help-block">{lang t="system|statements_in_pixel"}</p>
					</div>
				</div>
				<div class="form-group">
					<label for="height" class="col-lg-2 control-label">{lang t="gallery|image_height"}</label>
					<div class="col-lg-10">
						<input class="form-control" type="number" name="height" id="height" value="{$form.height}">
						<p class="help-block">{lang t="system|statements_in_pixel"}</p>
					</div>
				</div>
				<div class="form-group">
					<label for="maxwidth" class="col-lg-2 control-label">{lang t="gallery|max_image_width"}</label>
					<div class="col-lg-10">
						<input class="form-control" type="number" name="maxwidth" id="maxwidth" value="{$form.maxwidth}">
						<p class="help-block">{lang t="system|statements_in_pixel"}</p>
					</div>
				</div>
				<div class="form-group">
					<label for="maxheight" class="col-lg-2 control-label">{lang t="gallery|max_image_height"}</label>
					<div class="col-lg-10">
						<input class="form-control" type="number" name="maxheight" id="maxheight" value="{$form.maxheight}">
						<p class="help-block">{lang t="system|statements_in_pixel"}</p>
					</div>
				</div>
				<div class="form-group">
					<label for="filesize" class="col-lg-2 control-label">{lang t="gallery|image_filesize"}</label>
					<div class="col-lg-10">
						<input class="form-control" type="number" name="filesize" id="filesize" value="{$form.filesize}">
						<p class="help-block">{lang t="system|statements_in_byte"}</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-offset-2 col-lg-10">
			<button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
			<a href="{uri args="acp/gallery"}" class="btn btn-default">{lang t="system|cancel"}</a>
			{$form_token}
		</div>
	</div>
</form>