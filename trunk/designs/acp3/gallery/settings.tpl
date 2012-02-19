{if isset($error_msg)}
{$error_msg}
{/if}
<script type="text/javascript" src="{$DESIGN_PATH}gallery/settings.js"></script>
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<div id="tabs">
		<ul>
			<li><a href="#tab-1">{lang t="common|general_statements"}</a></li>
			<li><a href="#tab-2">{lang t="gallery|image_dimensions"}</a></li>
		</ul>
		<div id="tab-1">
			<dl>
				<dt><label for="date-format">{lang t="common|date_format"}</label></dt>
				<dd>
					<select name="form[dateformat]" id="date-format">
						<option value="">{lang t="common|pls_select"}</option>
{foreach $dateformat as $row}
						<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
					</select>
				</dd>
				<dt><label for="sidebar-entries">{lang t="common|sidebar_entries_to_display"}</label></dt>
				<dd>
					<select name="form[sidebar]" id="sidebar-entries">
						<option>{lang t="common|pls_select"}</option>
{foreach $sidebar_entries as $row}
						<option value="{$row.value}"{$row.selected}>{$row.value}</option>
{/foreach}
					</select>
				</dd>
				<dt>
					<label for="overlay-1">{lang t="gallery|use_overlay"}</label>
					<span>({lang t="gallery|use_overlay_description"})</span>
				</dt>
				<dd>
{foreach $overlay as $row}
					<label for="overlay-{$row.value}">
						<input type="radio" name="form[overlay]" id="overlay-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
						{$row.lang}
					</label>
{/foreach}
				</dd>
			</dl>
			<dl id="comments-container">
{if isset($comments)}
				<dt><label for="comments-1">{lang t="common|allow_comments"}</label></dt>
				<dd>
{foreach $comments as $row}
					<label for="comments-{$row.value}">
						<input type="radio" name="form[comments]" id="comments-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
						{$row.lang}
					</label>
{/foreach}
				</dd>
{/if}
			</dl>
		</div>
		<div id="tab-2">
			<dl>
				<dt><label for="thumbwidth">{lang t="gallery|thumb_image_width"}</label></dt>
				<dd><input type="number" name="form[thumbwidth]" id="thumbwidth" value="{$form.thumbwidth}"></dd>
				<dt><label for="thumbheight">{lang t="gallery|thumb_image_height"}</label></dt>
				<dd><input type="number" name="form[thumbheight]" id="thumbheight" value="{$form.thumbheight}"></dd>
				<dt><label for="width">{lang t="gallery|image_width"}</label></dt>
				<dd><input type="number" name="form[width]" id="width" value="{$form.width}"></dd>
				<dt><label for="height">{lang t="gallery|image_height"}</label></dt>
				<dd><input type="number" name="form[height]" id="height" value="{$form.height}"></dd>
				<dt><label for="maxwidth">{lang t="gallery|max_image_width"}</label></dt>
				<dd><input type="number" name="form[maxwidth]" id="maxwidth" value="{$form.maxwidth}"></dd>
				<dt><label for="maxheight">{lang t="gallery|max_image_height"}</label></dt>
				<dd><input type="number" name="form[maxheight]" id="maxheight" value="{$form.maxheight}"></dd>
				<dt><label for="filesize">{lang t="gallery|image_filesize"}</label></dt>
				<dd><input type="number" name="form[filesize]" id="filesize" value="{$form.filesize}"></dd>
			</dl>
		</div>
	</div>
	<div class="form-bottom">
		<input type="submit" value="{lang t="common|submit"}" class="form">
		{$form_token}
	</div>
</form>