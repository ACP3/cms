{if isset($error_msg)}
{$error_msg}
{/if}
<script type="text/javascript">
$(document).ready(function() {
	$('input[name="overlay"]').bind('click', function() {
		if ($(this).val() == 1) {
			$('#comments-container').hide();
		} else {
			$('#comments-container').show();
		}
	});

	$('input[name="overlay"]:checked').trigger('click');
});
</script>
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab-1" data-toggle="tab">{lang t="common|general_statements"}</a></li>
			<li><a href="#tab-2" data-toggle="tab">{lang t="gallery|image_dimensions"}</a></li>
		</ul>
		<div class="tab-content">
			<div id="tab-1" class="tab-pane active">
				<div class="control-group">
					<label for="date-format" class="control-label">{lang t="common|date_format"}</label>
					<div class="controls">
						<select name="dateformat" id="date-format">
							<option value="">{lang t="common|pls_select"}</option>
{foreach $dateformat as $row}
							<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
						</select>
					</div>
				</div>
				<div class="control-group">
					<label for="sidebar-entries" class="control-label">{lang t="common|sidebar_entries_to_display"}</label>
					<div class="controls">
						<select name="sidebar" id="sidebar-entries">
							<option>{lang t="common|pls_select"}</option>
{foreach $sidebar_entries as $row}
							<option value="{$row.value}"{$row.selected}>{$row.value}</option>
{/foreach}
						</select>
					</div>
				</div>
				<div class="control-group">
					<label for="overlay-1" class="control-label">{lang t="gallery|use_overlay"}</label>
					<div class="controls">
						<div class="btn-group" data-toggle="radio">
{foreach $overlay as $row}
							<input type="radio" name="overlay" id="overlay-{$row.value}" value="{$row.value}"{$row.checked}>
							<label for="overlay-{$row.value}" class="btn">{$row.lang}</label>
{/foreach}
						</div>
						<p class="help-block">{lang t="gallery|use_overlay_description"}</p>
					</div>
				</div>
{if isset($comments)}
				<div id="comments-container" class="control-group">
					<label for="comments-1" class="control-label">{lang t="common|allow_comments"}</label>
					<div class="controls">
						<div class="btn-group" data-toggle="radio">
{foreach $comments as $row}
							<input type="radio" name="comments" id="comments-{$row.value}" value="{$row.value}"{$row.checked}>
							<label for="comments-{$row.value}" class="btn">{$row.lang}</label>
{/foreach}
						</div>
					</div>
				</div>
{/if}
			</div>
			<div id="tab-2" class="tab-pane">
				<div class="control-group">
					<label for="thumbwidth" class="control-label">{lang t="gallery|thumb_image_width"}</label>
					<div class="controls">
						<input type="number" name="thumbwidth" id="thumbwidth" value="{$form.thumbwidth}">
						<p class="help-block">{lang t="common|statements_in_pixel"}</p>
					</div>
				</div>
				<div class="control-group">
					<label for="thumbheight" class="control-label">{lang t="gallery|thumb_image_height"}</label>
					<div class="controls">
						<input type="number" name="thumbheight" id="thumbheight" value="{$form.thumbheight}">
						<p class="help-block">{lang t="common|statements_in_pixel"}</p>
					</div>
				</div>
				<div class="control-group">
					<label for="width" class="control-label">{lang t="gallery|image_width"}</label>
					<div class="controls">
						<input type="number" name="width" id="width" value="{$form.width}">
						<p class="help-block">{lang t="common|statements_in_pixel"}</p>
					</div>
				</div>
				<div class="control-group">
					<label for="height" class="control-label">{lang t="gallery|image_height"}</label>
					<div class="controls">
						<input type="number" name="height" id="height" value="{$form.height}">
						<p class="help-block">{lang t="common|statements_in_pixel"}</p>
					</div>
				</div>
				<div class="control-group">
					<label for="maxwidth" class="control-label">{lang t="gallery|max_image_width"}</label>
					<div class="controls">
						<input type="number" name="maxwidth" id="maxwidth" value="{$form.maxwidth}">
						<p class="help-block">{lang t="common|statements_in_pixel"}</p>
					</div>
				</div>
				<div class="control-group">
					<label for="maxheight" class="control-label">{lang t="gallery|max_image_height"}</label>
					<div class="controls">
						<input type="number" name="maxheight" id="maxheight" value="{$form.maxheight}">
						<p class="help-block">{lang t="common|statements_in_pixel"}</p>
					</div>
				</div>
				<div class="control-group">
					<label for="filesize" class="control-label">{lang t="gallery|image_filesize"}</label>
					<div class="controls">
						<input type="number" name="filesize" id="filesize" value="{$form.filesize}">
						<p class="help-block">{lang t="common|statements_in_byte"}</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="form-actions">
		<button type="submit" name="submit" class="btn">{lang t="common|submit"}</button>
		<a href="{uri args="acp/gallery"}" class="btn">{lang t="common|cancel"}</a>
		{$form_token}
	</div>
</form>